<?php

namespace Servex\Core\Registry;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Service Registry
 * Manages service discovery and node information
 */
class ServiceRegistry
{
    private array $nodes = [];
    private array $services = [];
    private array $actions = [];
    private array $events = [];
    private LoggerInterface $logger;
    private string $strategy;
    private bool $preferLocal;
    private array $nodeSelectors = [];

    /**
     * ServiceRegistry constructor
     *
     * @param array $options
     * @param LoggerInterface|null $logger
     */
    public function __construct(array $options = [], ?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->strategy = $options['strategy'] ?? 'RoundRobin';
        $this->preferLocal = $options['preferLocal'] ?? true;
        
        // Register node selectors
        $this->nodeSelectors = [
            'RoundRobin' => new RoundRobinStrategy(),
            'Random' => new RandomStrategy(),
        ];
    }

    /**
     * Register a node
     *
     * @param string $nodeID
     * @param array $info
     */
    public function registerNode(string $nodeID, array $info): void
    {
        $this->nodes[$nodeID] = [
            'id' => $nodeID,
            'services' => $info['services'] ?? [],
            'available' => true,
            'lastHeartbeat' => time(),
            'cpu' => $info['cpu'] ?? 0,
            'timestamp' => $info['timestamp'] ?? time()
        ];
        
        $this->logger->info("Node registered: {$nodeID}");
        
        // Register services and actions from this node
        if (isset($info['services'])) {
            foreach ($info['services'] as $service) {
                $this->registerService($nodeID, $service);
            }
        }
    }

    /**
     * Register a service
     *
     * @param string $nodeID
     * @param array $serviceInfo
     */
    private function registerService(string $nodeID, array $serviceInfo): void
    {
        $serviceName = $serviceInfo['name'];
        
        // Register service
        if (!isset($this->services[$serviceName])) {
            $this->services[$serviceName] = [
                'name' => $serviceName,
                'nodes' => [],
                'actions' => [],
                'events' => []
            ];
        }
        
        // Add node to service
        $this->services[$serviceName]['nodes'][$nodeID] = $nodeID;
        
        // Register actions
        if (isset($serviceInfo['actions'])) {
            foreach ($serviceInfo['actions'] as $action) {
                $actionName = $action;
                $fullActionName = "{$serviceName}.{$actionName}";
                
                if (!isset($this->actions[$fullActionName])) {
                    $this->actions[$fullActionName] = [
                        'name' => $fullActionName,
                        'service' => $serviceName,
                        'action' => $actionName,
                        'nodes' => []
                    ];
                }
                
                $this->actions[$fullActionName]['nodes'][$nodeID] = $nodeID;
                $this->services[$serviceName]['actions'][$actionName] = $fullActionName;
            }
        }
        
        // Register events
        if (isset($serviceInfo['events'])) {
            foreach ($serviceInfo['events'] as $event) {
                $eventName = $event;
                
                if (!isset($this->events[$eventName])) {
                    $this->events[$eventName] = [
                        'name' => $eventName,
                        'service' => $serviceName,
                        'nodes' => []
                    ];
                }
                
                $this->events[$eventName]['nodes'][$nodeID] = $nodeID;
                $this->services[$serviceName]['events'][$eventName] = $eventName;
            }
        }
    }

    /**
     * Deregister a node
     *
     * @param string $nodeID
     */
    public function deregisterNode(string $nodeID): void
    {
        if (isset($this->nodes[$nodeID])) {
            unset($this->nodes[$nodeID]);
            $this->logger->info("Node deregistered: {$nodeID}");
            
            // Remove node from services and actions
            foreach ($this->services as $serviceName => $service) {
                if (isset($service['nodes'][$nodeID])) {
                    unset($this->services[$serviceName]['nodes'][$nodeID]);
                    
                    // If no nodes left for this service, remove it
                    if (empty($this->services[$serviceName]['nodes'])) {
                        unset($this->services[$serviceName]);
                    }
                }
            }
            
            foreach ($this->actions as $actionName => $action) {
                if (isset($action['nodes'][$nodeID])) {
                    unset($this->actions[$actionName]['nodes'][$nodeID]);
                    
                    // If no nodes left for this action, remove it
                    if (empty($this->actions[$actionName]['nodes'])) {
                        unset($this->actions[$actionName]);
                    }
                }
            }
            
            foreach ($this->events as $eventName => $event) {
                if (isset($event['nodes'][$nodeID])) {
                    unset($this->events[$eventName]['nodes'][$nodeID]);
                    
                    // If no nodes left for this event, remove it
                    if (empty($this->events[$eventName]['nodes'])) {
                        unset($this->events[$eventName]);
                    }
                }
            }
        }
    }

    /**
     * Get a node for an action
     *
     * @param string $actionName
     * @param string|null $localNodeID
     * @return string|null
     */
    public function getNodeForAction(string $actionName, ?string $localNodeID = null): ?string
    {
        if (!isset($this->actions[$actionName])) {
            return null;
        }
        
        $action = $this->actions[$actionName];
        $availableNodes = array_filter($action['nodes'], function($nodeID) {
            return isset($this->nodes[$nodeID]) && $this->nodes[$nodeID]['available'];
        });
        
        if (empty($availableNodes)) {
            return null;
        }
        
        // If prefer local is enabled and local node is available, use it
        if ($this->preferLocal && $localNodeID && isset($availableNodes[$localNodeID])) {
            return $localNodeID;
        }
        
        // Use the selected strategy to get a node
        $selector = $this->nodeSelectors[$this->strategy] ?? $this->nodeSelectors['RoundRobin'];
        return $selector->select($availableNodes, $actionName);
    }

    /**
     * Get all nodes for an event
     *
     * @param string $eventName
     * @return array
     */
    public function getNodesForEvent(string $eventName): array
    {
        if (!isset($this->events[$eventName])) {
            return [];
        }
        
        $event = $this->events[$eventName];
        return array_filter($event['nodes'], function($nodeID) {
            return isset($this->nodes[$nodeID]) && $this->nodes[$nodeID]['available'];
        });
    }

    /**
     * Get all services
     *
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * Get all nodes
     *
     * @return array
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * Get all actions
     *
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Get all events
     *
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * Update node heartbeat
     *
     * @param string $nodeID
     */
    public function heartbeat(string $nodeID): void
    {
        if (isset($this->nodes[$nodeID])) {
            $this->nodes[$nodeID]['lastHeartbeat'] = time();
            $this->nodes[$nodeID]['available'] = true;
        }
    }

    /**
     * Check for expired nodes
     *
     * @param int $heartbeatTimeout
     */
    public function checkNodes(int $heartbeatTimeout = 30): void
    {
        $now = time();
        foreach ($this->nodes as $nodeID => $node) {
            if ($now - $node['lastHeartbeat'] > $heartbeatTimeout) {
                $this->nodes[$nodeID]['available'] = false;
                $this->logger->warning("Node {$nodeID} is not available (heartbeat timeout)");
            }
        }
    }
} 