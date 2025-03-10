<?php

namespace Servex\Core\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface;

class AuthManager
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function authenticate(ServerRequestInterface $request): ?array
    {
        $token = $this->extractToken($request);
        if (!$token) {
            return null;
        }

        try {
            $decoded = JWT::decode(
                $token, 
                new Key($this->config['secret_key'], 'HS256')
            );
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function generateToken(array $userData): string
    {
        $issuedAt = time();
        $payload = array_merge(
            $userData,
            [
                'iat' => $issuedAt,
                'exp' => $issuedAt + $this->config['token_ttl']
            ]
        );

        return JWT::encode($payload, $this->config['secret_key'], 'HS256');
    }

    private function extractToken(ServerRequestInterface $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');
        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}