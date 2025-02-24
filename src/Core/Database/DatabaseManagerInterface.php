<?php

namespace Servex\Core\Database;

interface DatabaseManagerInterface
{
    public function getConnection(): \PDO;

    public function query(string $sql, array $params = []): array;
}