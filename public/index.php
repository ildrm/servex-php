<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Servex\Core\Application;
use Servex\Core\Auth\AuthManager;
use Servex\Core\Auth\Middleware\AuthMiddleware;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$configPath = __DIR__ . '/../config/config.php';
$app = new Application($configPath);

// Add middleware for authentication
$app->addMiddleware(new AuthMiddleware($app->getContainer()->get(AuthManager::class)));

// Example of service calls using the application's service manager
try {
    $serviceManager = $app->getServiceManager();
    
    // Create a new user
    $result = $serviceManager->call('user', 'create', ['Ali', 'ali@example.com', 'password123']);
    var_dump($result);

    // Get user by ID
    $user = $serviceManager->call('user', 'getUser', [1]);
    var_dump($user);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Run the application
$app->run();
