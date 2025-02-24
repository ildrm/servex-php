<?php

namespace Servex\Core;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Servex\Core\Cache\CacheManager;
use Servex\Core\Database\DatabaseManager;

class ServiceManager
{
    private ContainerInterface $container;
    private array $services = [];
    private EventBus $eventBus;
    private ?CacheManager $cache;
    private ?DatabaseManager $database;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->eventBus = new EventBus();
        $this->cache = $container->get(CacheManager::class);
        $this->database = $container->get(DatabaseManager::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function register(string $name, string $serviceClass): void
    {
        $service = $this->container->get($serviceClass);
        $this->services[$name] = $service;
        $service->setEventBus($this->eventBus);
    }

    public function get(string $name): ?object
    {
        return $this->services[$name] ?? null;
    }

    public function call(string $serviceName, string $action, array $params = []): mixed
    {
        $service = $this->get($serviceName);
        if ($service && method_exists($service, $action)) {
            return $service->$action(...$params);
        }
        throw new \RuntimeException("Service or action not found: {$serviceName}.{$action}");
    }

    public function getEventBus(): EventBus
    {
        return $this->eventBus;
    }

    public function getCache(): ?CacheManager
    {
        return $this->cache;
    }

    public function getDatabase(): ?DatabaseManager
    {
        return $this->database;
    }
}