<?php

namespace Servex\Core\Database;

use PDO;
use PDOException;

class DatabaseManager implements DatabaseManagerInterface
{
    private ?PDO $connection = null;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            try {
                $dsn = "mysql:host={$this->config['host']};dbname={$this->config['name']};charset=utf8mb4";
                $this->connection = new PDO(
                    $dsn,
                    $this->config['username'],
                    $this->config['password'],
                    $this->config['options'] ?? []
                );
            } catch (PDOException $e) {
                throw new DatabaseException("Connection failed: " . $e->getMessage());
            }
        }
        return $this->connection;
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}