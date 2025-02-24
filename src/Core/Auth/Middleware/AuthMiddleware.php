<?php

namespace Servex\Core\Auth\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Servex\Core\Auth\AuthManager;

class AuthMiddleware
{
    private AuthManager $auth;

    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $user = $this->auth->authenticate($request);
        if ($user === null) {
            $response = new \Laminas\Diactoros\Response();
            $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $userArray = (array) $user;
        $request = $request->withAttribute('user', $userArray);
        return $next($request);
    }
}