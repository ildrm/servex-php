<?php
// Example file to demonstrate usage of AuthManager

require_once 'vendor/autoload.php';

use Servex\Core\Auth\AuthManager;

// Create an instance of AuthManager with encryption key
$authManager = new AuthManager('your-secret-key-here');

// Define user data to store in token
$payload = [
    'user_id' => 123,
    'email' => 'example@email.com'
];

// Generate JWT token using user data
$token = $authManager->generateToken($payload);
echo "Generated token: " . $token . "\n";

// Validate token and get user information
$decoded = $authManager->verifyToken($token);
if ($decoded) {
    echo "Token is valid. User info:\n";
    print_r($decoded);
} else {
    echo "Token is invalid or expired.\n";
}