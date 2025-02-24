<?php

namespace Servex\Core\Database;

use PDO;

class DatabaseManager implements DatabaseManagerInterface
{
    private PDO $pdo;

    public function __construct(
        private string $host,
        private string $dbname,
        private string $username,
        private string $password,
        private array $options = []
    ) {
        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        
        $this->pdo = new PDO(
            "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
            $this->username,
            $this->password,
            array_merge($defaultOptions, $this->options)
        );
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}