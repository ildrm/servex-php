<?php

namespace Servex\Core\Cache;

use Psr\SimpleCache\CacheInterface;
use Predis\Client as RedisClient;

class CacheManager implements CacheInterface
{
    private RedisClient $cache;

    public function __construct(
        private string $host = 'localhost',
        private int $port = 6379,
        private ?string $password = null
    ) {
        $config = [
            'scheme' => 'tcp',
            'host' => $this->host,
            'port' => $this->port,
        ];
        
        if ($this->password) {
            $config['password'] = $this->password;
        }
        
        $this->cache = new RedisClient($config);
    }

    public function get($key, $default = null)
    {
        $value = $this->cache->get($key);
        return $value !== null ? unserialize($value) : $default;
    }

    public function set($key, $value, $ttl = null): bool
    {
        $serialized = serialize($value);
        if ($ttl !== null) {
            return (bool)$this->cache->setex($key, $ttl, $serialized);
        }
        return (bool)$this->cache->set($key, $serialized);
    }

    public function delete($key): bool
    {
        return (bool)$this->cache->del($key);
    }

    public function clear(): bool
    {
        return (bool)$this->cache->flushdb();
    }

    public function getMultiple($keys, $default = null)
    {
        $values = $this->cache->mget((array)$keys);
        $result = [];
        foreach ($keys as $i => $key) {
            $result[$key] = $values[$i] !== null ? unserialize($values[$i]) : $default;
        }
        return $result;
    }

    public function setMultiple($values, $ttl = null): bool
    {
        $serialized = array_map('serialize', $values);
        return (bool)$this->cache->mset($serialized);
    }

    public function deleteMultiple($keys): bool
    {
        return (bool)$this->cache->del($keys);
    }

    public function has($key): bool
    {
        return (bool)$this->cache->exists($key);
    }
}
