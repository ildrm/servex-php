<?php

namespace Servex\Core;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response;
use Servex\Core\Auth\AuthManager;
use Servex\Core\Cache\CacheManager;
use Servex\Core\Database\DatabaseManager;

class Application
{
    private Container $container;
    private ServiceManager $serviceManager;
    private array $middleware = [];
    
    public function __construct(string $configPath)
    {
        $config = require $configPath;
        $this->container = new Container();
        
        // Register core services
        $this->container->set(DatabaseManager::class, fn() => new DatabaseManager($config['database']));
        $this->container->set(CacheManager::class, fn() => new CacheManager($config['cache']));
        $this->container->set(AuthManager::class, fn() => new AuthManager($config['auth']));
        
        $this->serviceManager = new ServiceManager($this->container);
    }
    
    public function addMiddleware(callable $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }
    
    public function run(): void
    {
        $request = ServerRequestFactory::fromGlobals();
        $response = $this->handle($request);
        
        $this->emit($response);
    }
    
    private function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = function (ServerRequestInterface $request) {
            try {
                $path = $request->getUri()->getPath();
                $method = $request->getMethod();
                
                // Here you can add routing logic
                // For now, returning a simple JSON response
                $response = new Response();
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'message' => 'Servex PHP Application Running'
                ]));
                
                return $response->withHeader('Content-Type', 'application/json');
            } catch (\Exception $e) {
                $response = new Response();
                $response->getBody()->write(json_encode([
                    'error' => $e->getMessage()
                ]));
                return $response->withStatus(500)
                    ->withHeader('Content-Type', 'application/json');
            }
        };
        
        // Apply middleware in reverse order
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            function ($next, $middleware) {
                return function (ServerRequestInterface $request) use ($next, $middleware) {
                    return $middleware($request, $next);
                };
            },
            $handler
        );
        
        return $pipeline($request);
    }
    
    private function emit(ResponseInterface $response): void
    {
        $statusLine = sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );
        header($statusLine, true);
        
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }
        
        echo $response->getBody();
    }
    
    public function getServiceManager(): ServiceManager
    {
        return $this->serviceManager;
    }
    
    public function getContainer(): Container
    {
        return $this->container;
    }
}
