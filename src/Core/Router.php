<?php

namespace Servex\Core;

use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;

class Router
{
    private array $routes = [];
    private const ALLOWED_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'];

    public function get(string $path, callable $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, callable $handler): self
    {
        return $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, callable $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    public function options(string $path, callable $handler): self
    {
        return $this->addRoute('OPTIONS', $path, $handler);
    }

    public function head(string $path, callable $handler): self
    {
        return $this->addRoute('HEAD', $path, $handler);
    }

    public function addRoute(string $method, string $path, callable $handler): self
    {
        if (!in_array(strtoupper($method), self::ALLOWED_METHODS)) {
            throw new \InvalidArgumentException('Invalid HTTP method');
        }
        
        $this->routes[strtoupper($method)][$path] = $handler;
        return $this;
    }

    public function dispatch(ServerRequestInterface $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        if (isset($this->routes[$method][$path])) {
            return $this->routes[$method][$path]($request);
        }

        $response = new Response();
        $response->getBody()->write(json_encode([
            'error' => 'Route not found',
            'path' => $path,
            'method' => $method
        ]));
        
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json');
    }
}
