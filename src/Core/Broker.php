<?php

namespace Servex\Core;

use Servex\Core\Transport\TransportInterface;
use Servex\Core\Cache\CacheManager;
use Servex\Core\Config\ConfigManager;
use Servex\Core\Registry\ServiceRegistry;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Broker class is the main component of the Servex framework
 * It manages services, events, and network communication
 */
class Broker
{
    private ContainerInterface $container;
    private array $services = [];
    private EventBus $eventBus;
    private ?TransportInterface $transport = null;
    private ?CacheManager $cache = null;
    private LoggerInterface $logger;
    private array $middlewares = [];
    private ServiceRegistry $registry;
    private string $nodeID;
    private array $options;

    /**
     * Broker constructor
     *
     * @param ContainerInterface $container
     * @param array $options
     */
    public function __construct(ContainerInterface $container, array $options = [])
    {
        $this->container = $container;
$this->options = array_merge([
    'middlewares' => [], // Added support for middlewares

            'namespace' => '',
            'nodeID' => uniqid('node-'),
            'transporter' => null,
            'cacher' => null,
            'logger' => null,
            'metrics' => false,
            'retryPolicy' => [
                'enabled' => false,
                'retries' => 5,
                'delay' => 100,
                'maxDelay' => 2000,
                'factor' => 2
            ],
            'registry' => [
                'strategy' => 'RoundRobin', // RoundRobin, Random, CpuUsage
                'preferLocal' => true
            ]
        ], $options);

        $this->nodeID = $this->options['nodeID'];
        $this->logger = $this->options['logger'] ?? new NullLogger();
        $this->eventBus = new EventBus($this->logger);
        $this->registry = new ServiceRegistry($this->options['registry'], $this->logger);
        
        if ($this->options['cacher']) {
            $this->cache = $container->get(CacheManager::class);
        }
        
        if ($this->options['transporter']) {
            $this->transport = $container->get($this->options['transporter']);
        }
        
        // Register internal middlewares
        $this->use($this->callMiddleware());
        $retryMiddleware = new \Servex\Core\Middleware\RetryMiddleware($this->options['retryPolicy'], $this->logger);
        $this->use(function ($ctx) use ($retryMiddleware) {
            return $retryMiddleware->process($ctx);
        }); // Added retry middleware



    }

    /**
     * Start the broker
     */
    public function start(): void
    {
        $this->logger->info("Broker starting with nodeID: {$this->nodeID}");
        
        // Initialize services
        foreach ($this->services as $service) {
            if (method_exists($service, 'created')) {
                $service->created();
            }
        }
        
        // Connect to transporter if available
        if ($this->transport) {
            $this->transport->connect();
            $this->broadcastNodeInfo();
            
            // Subscribe to node info events
            $this->transport->subscribe('node.info', function($payload) {
                if ($payload['sender'] !== $this->nodeID) {
                    $this->registry->registerNode($payload['sender'], $payload);
                }
            });
            
            // Subscribe to heartbeat events
            $this->transport->subscribe('heartbeat', function($payload) {
                if ($payload['sender'] !== $this->nodeID) {
                    $this->registry->heartbeat($payload['sender']);
                }
            });
            
            // Subscribe to events
            $this->transport->subscribe('event', function($payload) {
                if ($payload['sender'] !== $this->nodeID) {
                    $this->eventBus->emit($payload['event'], $payload['data']);
                }
            });
        }
$this->logger->info("Starting services...");
foreach ($this->services as $service) {

            if (method_exists($service, 'started')) {
                $service->started();
            }
        }
        
$this->logger->info("Broker started successfully with nodeID: {$this->nodeID}");

    }

    /**
     * Stop the broker
     */
    public function stop(): void
    {
        $this->logger->info("Broker stopping...");
        
        // Stop services
        foreach ($this->services as $service) {
            if (method_exists($service, 'stopped')) {
                $service->stopped();
            }
        }
        
        // Disconnect from transporter
        if ($this->transport) {
            $this->transport->disconnect();
        }
        
        $this->logger->info("Broker stopped");
    }

    /**
     * Register a service
     *
     * @param object $service
     * @return self
     */
    public function createService(object $service): self
    {
        $serviceName = $service::class;
        $this->services[$serviceName] = $service;
        
        if (method_exists($service, 'setEventBus')) {
            $service->setEventBus($this->eventBus);
        }
        
        if (method_exists($service, 'setBroker')) {
            $service->setBroker($this);
        }
        
        $this->logger->info("Service registered: {$serviceName}");
        return $this;
    }

    /**
     * Call a service action
     *
     * @param string $actionName Format: "service.action"
     * @param array $params
     * @return mixed
     * @throws \RuntimeException
     */
    public function call(string $actionName, array $params = []): mixed
    {
        $this->logger->debug("Call action: {$actionName}", $params);
        
        // Apply request middlewares
        $ctx = [
            'action' => $actionName,
            'params' => $params,
            'nodeID' => $this->nodeID,
            'caller' => null
        ];
        
        foreach ($this->middlewares as $middleware) {
            $ctx = $middleware($ctx) ?? $ctx;
        }
        
        // Parse action name (service.action)
        $parts = explode('.', $actionName);
        if (count($parts) !== 2) {
            throw new \RuntimeException("Invalid action name format. Use 'service.action'");
        }
        
        [$serviceName, $action] = $parts;
        
        // Check if we have the service locally
        foreach ($this->services as $service) {
            $className = (new \ReflectionClass($service))->getShortName();
            if (strtolower($className) === strtolower($serviceName)) {
                if (method_exists($service, $action)) {
                    $result = $service->$action(...array_values($params));
                    
                    // Cache the result if caching is enabled
                    if ($this->cache && isset($this->options['cacher'])) {
                        $cacheKey = "action:{$actionName}:" . md5(json_encode($params));
                        $this->cache->set($cacheKey, $result, $this->options['cacher']['ttl'] ?? 3600);
                    }
                    
                    return $result;
                }
            }
        }
        
        // If not found locally and we have transport, call remotely
        if ($this->transport) {
            // Get node for this action from registry
            $targetNodeID = $this->registry->getNodeForAction($actionName, $this->nodeID);
            
            if ($targetNodeID) {
                return $this->transport->call($actionName, $params);
            }
        }
        
        throw new \RuntimeException("Service or action not found: {$actionName}");
    }

    /**
     * Emit an event
     *
     * @param string $eventName
     * @param array $data
     */
    public function emit(string $eventName, array $data = []): void
    {
        $this->eventBus->emit($eventName, $data);
        
        // Broadcast event to other nodes if transport is available
        if ($this->transport) {
            $this->transport->broadcast('event', [
                'sender' => $this->nodeID,
                'event' => $eventName,
                'data' => $data
            ]);
        }
    }

    /**
     * Subscribe to an event
     *
     * @param string $eventName
     * @param callable $callback
     */
    public function on(string $eventName, callable $callback): void
    {
        $this->eventBus->on($eventName, $callback);
    }

    /**
     * Add middleware
     *
     * @param callable $middleware
     * @return self
     */
    public function use(callable $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Get all registered services
     *
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * Get the event bus
     *
     * @return EventBus
     */
    public function getEventBus(): EventBus
    {
        return $this->eventBus;
    }

    /**
     * Get the cache manager
     *
     * @return CacheManager|null
     */
    public function getCache(): ?CacheManager
    {
        return $this->cache;
    }

    /**
     * Get the container
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Get the node ID
     *
     * @return string
     */
    public function getNodeID(): string
    {
        return $this->nodeID;
    }

    /**
     * Get the service registry
     *
     * @return ServiceRegistry
     */
    public function getRegistry(): ServiceRegistry
    {
        return $this->registry;
    }

    /**
     * Broadcast node info to other nodes
     */
    private function broadcastNodeInfo(): void
    {
        if (!$this->transport) {
            return;
        }
        
        $info = [
            'sender' => $this->nodeID,
            'nodeID' => $this->nodeID,
            'services' => array_map(function ($service) {
                $className = (new \ReflectionClass($service))->getShortName();
                $actions = [];
                
                foreach (get_class_methods($service) as $method) {
                    if ($method[0] !== '_' && !in_array($method, ['__construct', 'setEventBus', 'setBroker', 'created', 'started', 'stopped'])) {
                        $actions[] = $method;
                    }
                }
                
                return [
                    'name' => $className,
                    'actions' => $actions
                ];
            }, $this->services),
            'timestamp' => time()
        ];
        
        $this->transport->broadcast('node.info', $info);
    }

    /**
     * Create call middleware
     *
     * @return callable
     */
    private function callMiddleware(): callable
    {
        return function ($ctx) {
            // Implement caching if enabled
            if ($this->cache && isset($ctx['action'])) {
                $cacheKey = "action:{$ctx['action']}:" . md5(json_encode($ctx['params']));
                $cached = $this->cache->get($cacheKey);
                
                if ($cached !== null) {
                    $this->logger->debug("Using cached result for: {$ctx['action']}");
                    return $cached;
                }
                
                // Continue with the call and cache the result later
            }
            
            return $ctx;
        };
    }
}
