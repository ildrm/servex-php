<?php
/**
 * Example file to demonstrate usage of Broker and Services
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Servex\Core\Broker;
use Servex\Core\Container;
use Servex\Core\Config\ConfigManager;
use Servex\Core\Cache\CacheManager;
use Servex\Core\Database\DatabaseManager;
use Servex\Core\Transport\HttpTransport;
use Servex\Services\UserService;
use Psr\Log\LoggerInterface;
use Monolog\Logger;

use Monolog\Handler\StreamHandler;


// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Create logger
$logger = new Logger('servex');

$logger->pushHandler(new StreamHandler('php://stdout', MonologLogger::DEBUG));


// Create container
$container = new Container();

// Register services in container
$container->set(LoggerInterface::class, fn() => $logger);

$container->set(CacheManager::class, function() {
    return new CacheManager(
        $_ENV['CACHE_HOST'] ?? 'localhost',
        $_ENV['CACHE_PORT'] ?? 6379,
        $_ENV['CACHE_PASSWORD'] ?? null
    );
});

$container->set(DatabaseManager::class, function() {
    return new DatabaseManager(
        $_ENV['DB_HOST'] ?? 'localhost',
        $_ENV['DB_NAME'] ?? 'servex',
        $_ENV['DB_USER'] ?? 'root',
        $_ENV['DB_PASSWORD'] ?? '',
        []
    );
});

$container->set(HttpTransport::class, function() use ($logger) {
    return new HttpTransport(
        $_ENV['API_ENDPOINT'] ?? 'http://localhost:3000',
        $logger
    );
});

$container->set(UserService::class, function() use ($container) {
    return new UserService(
        $container->get(DatabaseManager::class),
        $container->get(CacheManager::class),
        ['logger' => $container->get(LoggerInterface::class)]
    );
});

// Create broker
$broker = new Broker($container, [
    'nodeID' => $_ENV['NODE_ID'] ?? 'node-1',
    'transporter' => HttpTransport::class,
    'cacher' => [
        'ttl' => 3600
    ],
    'logger' => $logger,
    'registry' => [
        'strategy' => 'RoundRobin',
        'preferLocal' => true
    ]
]);

// Register services
$broker->createService($container->get(UserService::class));

// Subscribe to events
$broker->on('user.created', function(array $data) use ($logger) {
    $logger->info("User created event received", $data);
});

// Start broker
$broker->start();

// Create a user
try {
    $user = $broker->call('user.create', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret'
    ]);
    
    echo "User created: " . json_encode($user) . PHP_EOL;
    
    // Get the user
    $user = $broker->call('user.get', ['id' => $user['id']]);
    echo "User retrieved: " . json_encode($user) . PHP_EOL;
    
    // Update the user
    $user = $broker->call('user.update', [
        'id' => $user['id'],
        'data' => ['name' => 'John Updated']
    ]);
    echo "User updated: " . json_encode($user) . PHP_EOL;
    
    // List users
    $users = $broker->call('user.list', ['limit' => 10, 'offset' => 0]);
    echo "Users list: " . json_encode($users) . PHP_EOL;
    
    // Delete the user
    $result = $broker->call('user.delete', ['id' => $user['id']]);
    echo "User deleted: " . ($result ? 'true' : 'false') . PHP_EOL;
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

