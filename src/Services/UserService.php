<?php

namespace Servex\Services;

use Servex\Core\EventBus;
use Servex\Core\Database\DatabaseManagerInterface;
use Servex\Core\Cache\CacheManager;

class UserService
{
    private EventBus $eventBus;
    private DatabaseManagerInterface $database;
    private CacheManager $cache;

    public function __construct(DatabaseManagerInterface $database, CacheManager $cache)
    {
        $this->database = $database;
        $this->cache = $cache;
    }

    public function setEventBus(EventBus $eventBus): void
    {
        $this->eventBus = $eventBus;
    }

    public function createUser(string $name): array
    {
        $user = ['id' => rand(1, 1000), 'name' => $name];
        $this->database->query("INSERT INTO users (id, name) VALUES (?, ?)", [$user['id'], $user['name']]);
        $this->cache->set("user_{$user['id']}", $user, 3600);
        $this->eventBus->emit('user.created', $user);
        return $user;
    }

    public function getUser(int $id): array
    {
        $cacheKey = "user_{$id}";
        $cachedUser = $this->cache->get($cacheKey);
        if ($cachedUser) {
            return $cachedUser;
        }

        $users = $this->database->query("SELECT * FROM users WHERE id = ?", [$id]);
        if (!empty($users)) {
            $user = $users[0];
            $this->cache->set($cacheKey, $user, 3600);
            return $user;
        }

        throw new \RuntimeException("User not found.");
    }

    public function getCache(): CacheManager
    {
        return $this->cache;
    }

    public function getDatabase(): DatabaseManagerInterface
    {
        return $this->database;
    }
}