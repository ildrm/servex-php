<?php

namespace Servex;

use Servex\Core\ServiceManager;
use Servex\Core\Container;
use Servex\Core\Config\ConfigManager;
use Servex\Services\UserService;
use Servex\Core\Cache\CacheManager;
use Servex\Core\Database\DatabaseManager;
use Servex\Core\Auth\AuthManager;

class Bootstrap
{
    private ServiceManager $serviceManager;

    public function __construct(string $configPath)
    {
        ConfigManager::load($configPath);
        
        $container = new Container();
        
        $container->set(CacheManager::class, function() {
            return new CacheManager(
                ConfigManager::get('cache.host'),
                ConfigManager::get('cache.port'),
                ConfigManager::get('cache.password')
            );
        });
        
        $container->set(DatabaseManager::class, function() {
            return new DatabaseManager(
                ConfigManager::get('database.host'),
                ConfigManager::get('database.name'),
                ConfigManager::get('database.username'),
                ConfigManager::get('database.password'),
                ConfigManager::get('database.options', [])
            );
        });
        
        $container->set(AuthManager::class, function() {
            return new AuthManager(
                ConfigManager::get('auth.secret_key')
            );
        });
        
        $container->set(UserService::class, fn() => new UserService(
            $container->get(DatabaseManager::class),
            $container->get(CacheManager::class)
        ));

        $this->serviceManager = new ServiceManager($container);
    }

    public function init(): void
    {
        $this->serviceManager->register('user', UserService::class);

        $this->serviceManager->getEventBus()->on('user.created', function (array $data) {
            echo "User created: " . $data['name'] . PHP_EOL;
        });
    }

    public function getServiceManager(): ServiceManager
    {
        return $this->serviceManager;
    }
}