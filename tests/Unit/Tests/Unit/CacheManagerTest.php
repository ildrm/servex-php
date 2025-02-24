<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Servex\Core\Cache\CacheManager;

class CacheManagerTest extends TestCase
{
    private CacheManager $cache;

    protected function setUp(): void
    {
        $mockCache = $this->createMock(CacheInterface::class);
        $this->cache = new CacheManager('localhost', 6379, null);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testSetAndGetCache(): void
    {
        $key = 'test_key';
        $value = 'test_value';
        $this->cache->set($key, $value, 3600);
        $result = $this->cache->get($key);
        $this->assertEquals($value, $result);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCacheHas(): void
    {
        $key = 'test_key';
        $this->cache->set($key, 'test_value', 3600);
        $this->assertTrue($this->cache->has($key));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testDeleteCache(): void
    {
        $key = 'test_key';
        $this->cache->set($key, 'test_value', 3600);
        $this->cache->delete($key);
        $this->assertFalse($this->cache->has($key));
    }
}