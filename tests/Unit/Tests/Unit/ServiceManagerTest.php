<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Servex\Core\Container;
use Servex\Core\ServiceManager;
use Servex\Core\Cache\CacheManager;
use Servex\Core\Database\DatabaseManager;
use Servex\Services\UserService;

class ServiceManagerTest extends TestCase
{
    private ServiceManager $serviceManager;
    private Container $container;

    protected function setUp(): void
    {
        // Create a new container
        $this->container = new Container();
        
        // Mock CacheManager
        $mockCache = $this->createMock(CacheManager::class);
        $this->container->set(CacheManager::class, fn() => $mockCache);
        
        // Mock DatabaseManager
        $mockDatabase = $this->createMock(DatabaseManager::class);
        $this->container->set(DatabaseManager::class, fn() => $mockDatabase);
        
        // Create a test service
        $mockUserService = $this->createMock(UserService::class);
        $mockUserService->method('createUser')->willReturn(['id' => 1, 'name' => 'Test User']);
        $this->container->set(UserService::class, fn() => $mockUserService);
        
        // Initialize ServiceManager with the container
        $this->serviceManager = new ServiceManager($this->container);
    }

    public function testRegisterAndGetService(): void
    {
        $this->serviceManager->register('user', UserService::class);
        $service = $this->serviceManager->get('user');
        $this->assertInstanceOf(UserService::class, $service);
    }

    public function testCallServiceMethod(): void
    {
        // Setup mock expectations
        $mockUserService = $this->createMock(UserService::class);
        $mockUserService->expects($this->once())
            ->method('createUser')
            ->with('Test User')
            ->willReturn(['id' => 1, 'name' => 'Test User']);
            
        // Register the mock service
        $this->container->set(UserService::class, fn() => $mockUserService);
        $this->serviceManager->register('user', UserService::class);
        
        // Test the call
        $result = $this->serviceManager->call('user', 'createUser', ['Test User']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals('Test User', $result['name']);
    }

    public function testGetEventBus(): void
    {
        $eventBus = $this->serviceManager->getEventBus();
        $this->assertNotNull($eventBus);
    }

    public function testServiceNotFoundThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->serviceManager->call('nonexistent', 'someMethod');
    }

    public function testGetCache(): void
    {
        $cache = $this->serviceManager->getCache();
        $this->assertInstanceOf(CacheManager::class, $cache);
    }

    public function testGetDatabase(): void
    {
        $database = $this->serviceManager->getDatabase();
        $this->assertInstanceOf(DatabaseManager::class, $database);
    }
}