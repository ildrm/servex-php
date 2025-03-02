<?php

namespace Servex\Core;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Base Service class
 * All services should extend this class
 */
abstract class Service
{
    protected string $name;
    protected array $settings = [];
    protected ?Broker $broker = null;
    protected ?EventBus $eventBus = null;
    protected LoggerInterface $logger;
    protected array $actions = []; // Actions registered for the service
    protected array $events = [];

    /**
     * Service constructor
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = array_merge($this->getDefaultSettings(), $settings);
        $this->name = $this->settings['name'] ?? $this->getServiceName();
        $this->logger = $this->settings['logger'] ?? new NullLogger();
        
        $this->registerActions();
        $this->registerEvents();
    }

    /**
     * Get default settings for the service
     *
     * @return array
     */
    protected function getDefaultSettings(): array
    {
        return [
            'name' => null,
            'version' => 1,
            'logger' => null,
        ];
    }

    /**
     * Get the service name
     * By default, it uses the class short name
     *
     * @return string
     */
    protected function getServiceName(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        // Remove "Service" suffix if exists
        if (substr($className, -7) === 'Service') {
            $className = substr($className, 0, -7);
        }
        return strtolower($className);
    }

    /**
     * Register service actions
     * This method scans the class for public methods that don't start with underscore
     */
    protected function registerActions(): void
    {
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            // Skip methods that start with underscore or are inherited from the base class
            if ($method[0] === '_' || in_array($method, [
                '__construct', 'getDefaultSettings', 'getServiceName', 'registerActions',
                'registerEvents', 'setEventBus', 'setBroker', 'created', 'started', 'stopped'
            ])) {
                continue;
            }
            
            $this->actions[$method] = [
                'name' => $method,
                'handler' => [$this, $method],
                'cache' => false,
                'params' => []
            ];
        }
    }

    /**
     * Register service events
     * Override this method to register event handlers
     */
    protected function registerEvents(): void
    {
        // Register event handlers here
    }

    /**
     * Set the event bus
     *
     * @param EventBus $eventBus
     * @return self
     */
    public function setEventBus(EventBus $eventBus): self
    {
        $this->eventBus = $eventBus;
        return $this;
    }

    /**
     * Set the broker
     *
     * @param Broker $broker
     * @return self
     */
    public function setBroker(Broker $broker): self
    {
        $this->broker = $broker;
        return $this;
    }

    /**
     * Emit an event
     *
     * @param string $eventName
     * @param array $data
     */
    protected function emit(string $eventName, array $data = []): void
    {
        if ($this->eventBus) {
            // Prefix event name with service name if it doesn't have a dot
            if (strpos($eventName, '.') === false) {
                $eventName = "{$this->name}.{$eventName}";
            }
            
            $this->eventBus->emit($eventName, $data);
        }
    }

    /**
     * Call another service action
     *
     * @param string $actionName Format: "service.action"
     * @param array $params
     * @return mixed
     */
    protected function call(string $actionName, array $params = []): mixed
    {
        if (!$this->broker) {
            throw new \RuntimeException("Broker is not set. Cannot call action: {$actionName}");
        }
        
        return $this->broker->call($actionName, $params);
    }

    /**
     * Called when service is created
     * Override this method for initialization
     */
    public function created(): void
    {
        // To be implemented by child classes
    }

    /**
     * Called when broker is started
     * Override this method for startup logic
     */
    public function started(): void
    {
        // To be implemented by child classes
    }

    /**
     * Called when broker is stopped
     * Override this method for cleanup logic
     */
    public function stopped(): void
    {
        // To be implemented by child classes
    }
}
