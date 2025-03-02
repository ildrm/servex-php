<?php

namespace Servex\Services;

use Servex\Core\Service;
use Servex\Core\Database\DatabaseManagerInterface;
use Servex\Core\Cache\CacheManager;

class UserService extends Service
{
    private DatabaseManagerInterface $database;
    private CacheManager $cache;

    /**
     * UserService constructor
     *
     * @param DatabaseManagerInterface $database
     * @param CacheManager $cache
     * @param array $settings
     */
    public function __construct(DatabaseManagerInterface $database, CacheManager $cache, array $settings = [])
    {
        $this->database = $database;
        $this->cache = $cache;
        
        parent::__construct($settings);
    }

    /**
     * Register service events
     */
    protected function registerEvents(): void
    {
        if ($this->eventBus) {
            $this->eventBus->on('user.deleted', function(array $data) {
                $this->logger->info("User deleted event received", $data);
                // Handle user deleted event
            });
        }
    }

    /**
     * Create a new user
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return array
     */
    public function create(string $name, string $email, string $password): array
    {
        $user = [
            'id' => rand(1, 1000), 
            'name' => $name,
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->database->query(
            "INSERT INTO users (id, name, email, password) VALUES (?, ?, ?, ?)", 
            [$user['id'], $user['name'], $email, password_hash($password, PASSWORD_DEFAULT)]
        );
        
        $this->cache->set("user_{$user['id']}", $user, 3600);
        
        // Emit event using the new emit method from Service base class
        $this->emit('created', $user);
        
        return $user;
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return array
     * @throws \RuntimeException
     */
    public function get(int $id): array
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

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return array
     * @throws \RuntimeException
     */
    public function update(int $id, array $data): array
    {
        // First get the user to make sure it exists
        $user = $this->get($id);
        
        // Build update query
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'email'])) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
                $user[$key] = $value;
            }
        }
        
        if (empty($fields)) {
            return $user;
        }
        
        $values[] = $id;
        $this->database->query(
            "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?", 
            $values
        );
        
        // Update cache
        $this->cache->set("user_{$id}", $user, 3600);
        
        // Emit event
        $this->emit('updated', $user);
        
        return $user;
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        // First get the user to make sure it exists
        try {
            $user = $this->get($id);
        } catch (\RuntimeException $e) {
            return false;
        }
        
        $this->database->query("DELETE FROM users WHERE id = ?", [$id]);
        $this->cache->delete("user_{$id}");
        
        // Emit event
        $this->emit('deleted', ['id' => $id]);
        
        return true;
    }

    /**
     * List all users
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function list(int $limit = 10, int $offset = 0): array
    {
        return $this->database->query(
            "SELECT * FROM users LIMIT ? OFFSET ?", 
            [$limit, $offset]
        );
    }
}
