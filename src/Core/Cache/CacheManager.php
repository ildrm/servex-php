<?php

namespace Servex\Core\Cache;

use Psr\SimpleCache\CacheInterface;

class CacheManager implements CacheInterface 
{
    private string $cachePath;
    
    public function __construct(array $config)
    {
        $this->cachePath = $config['path'] ?? sys_get_temp_dir() . '/servex-cache';
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $path = $this->getPath($key);
        if (!file_exists($path)) {
            return $default;
        }
        return unserialize(file_get_contents($path));
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        return file_put_contents($this->getPath($key), serialize($value)) !== false;
    }

    private function getPath(string $key): string
    {
        return $this->cachePath . '/' . md5($key);
    }

    public function delete(string $key): bool
    {
        $path = $this->getPath($key);
        return file_exists($path) && unlink($path);
    }

    public function clear(): bool
    {
        $files = glob($this->cachePath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    public function setMultiple($values, $ttl = null): bool
    {
        $result = true;
        foreach ($values as $key => $value) {
            $result = $result && $this->set($key, $value, $ttl);
        }
        return $result;
    }

    public function deleteMultiple($keys): bool
    {
        $result = true;
        foreach ($keys as $key) {
            $result = $result && $this->delete($key);
        }
        return $result;
    }

    public function has($key): bool
    {
        return file_exists($this->getPath($key));
    }
}
