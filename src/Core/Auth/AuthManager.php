<?php

namespace Servex\Core\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

class AuthManager
{
    private string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function generateToken(array $payload, int $ttl = 3600): string
    {
        $payload['exp'] = time() + $ttl;
        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function verifyToken(string $token): ?stdClass
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded instanceof stdClass ? $decoded : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function authenticate(ServerRequestInterface $request): ?stdClass
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $this->verifyToken($matches[1]);
        }
        return null;
    }
}