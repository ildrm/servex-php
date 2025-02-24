<?php

namespace Servex\Core\Cache;

use InvalidArgumentException;

interface CacheInterface
{
    /**
     * Fetches a value from the cache.
     *
     * @param string $key The unique key of this item in the cache.
     * @param mixed $default Default value to return if the key does not exist.
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     * @throws InvalidArgumentException If the $key string is not a legal value.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Persists data in the cache, uniquely referenced by a key.
     *
     * @param string $key The key of the item to store.
     * @param mixed $value The value of the item to store.
     * @param null|int|\DateInterval $ttl Optional. The TTL value of this item.
     * @return bool True on success and false on failure.
     * @throws InvalidArgumentException If the $key string is not a legal value.
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool;

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     * @return bool True if the item was successfully removed, false otherwise.
     * @throws InvalidArgumentException If the $key string is not a legal value.
     */
    public function delete(string $key): bool;

    /**
     * Determines whether an item is present in the cache.
     *
     * @param string $key The cache item key.
     * @return bool True if cache entry exists, false otherwise.
     * @throws InvalidArgumentException If the $key string is not a legal value.
     */
    public function has(string $key): bool;
}
