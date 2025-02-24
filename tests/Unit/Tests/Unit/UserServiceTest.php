<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\InvalidArgumentException;
use Servex\Services\UserService;
use Servex\Core\EventBus;
use Servex\Core\Cache\CacheManager;
use Servex\Core\Database\DatabaseManagerInterface;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private EventBus $eventBus;

    protected function setUp(): void
    {
        $this->eventBus = new EventBus();
        $cache = new CacheManager();
        $cache->clear(); // Clear cache before each test
        
        /** @var DatabaseManagerInterface */
        $database = $this->createMock(DatabaseManagerInterface::class);
        $database->method('query')->willReturn([['id' => 1, 'name' => 'Test User']]);
        
        $this->userService = new UserService($database, $cache);
        $this->userService->setEventBus($this->eventBus);
    }

    public function testCreateUser(): void
    {
        $result = $this->userService->createUser('Test User');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals('Test User', $result['name']);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetUserFromCache(): void
    {
        $cache = $this->userService->getCache();
        $cache->set('user_1', ['id' => 1, 'name' => 'Cached User']);
        $result = $this->userService->getUser(1);
        $this->assertEquals(['id' => 1, 'name' => 'Cached User'], $result);
    }

    public function testGetUserFromDatabase(): void
    {
        $result = $this->userService->getUser(1);
        $this->assertEquals(['id' => 1, 'name' => 'Test User'], $result);
    }

    public function testGetDatabase(): void
    {
        $database = $this->userService->getDatabase();
        $this->assertInstanceOf(DatabaseManagerInterface::class, $database);
    }
}