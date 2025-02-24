<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Servex\Core\Database\DatabaseManager;

class DatabaseManagerTest extends TestCase
{
    private DatabaseManager $database;

    protected function setUp(): void
    {
        $this->database = new DatabaseManager(
            'localhost',
            'servex_db',
            'root',
            '',
            []
        );
    }

    public function testQuery(): void
    {
        $result = $this->database->query("SELECT * FROM users WHERE id = ?", [1]);
        $this->assertIsArray($result);
        if (!empty($result)) {
            $this->assertArrayHasKey('id', $result[0]);
        }
    }

    public function testConnection(): void
    {
        $pdo = $this->database->getConnection();
        $this->assertInstanceOf(\PDO::class, $pdo);
    }
}