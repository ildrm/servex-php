<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Servex\Bootstrap;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$configPath = __DIR__ . '/../config/config.php';

$bootstrap = new Bootstrap($configPath);
$bootstrap->init();

try {
    $serviceManager = $bootstrap->getServiceManager();
    $result = $serviceManager->call('user', 'create', ['Ali', 'ali@example.com', 'password123']); // Updated to include email and password


    var_dump($result);

    $user = $serviceManager->call('user', 'getUser', [1]);
    var_dump($user);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
