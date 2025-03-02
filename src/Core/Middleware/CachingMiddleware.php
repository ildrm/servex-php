<?php

namespace Servex\Core\Middleware;

use Servex\Core\Cache\CacheManager;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Caching Middleware
 * Caches action results
 */
class CachingMiddleware implements MiddlewareInterface
{
    private CacheManager $cache;
    private LoggerInterface $logger;
    private array $options;

    /**
     * CachingMiddleware constructor
     *
     * @param CacheManager $cache
     * @param array $options
     * @param LoggerInterface|null $logger
     */
    public function __construct(CacheManager $cache, array $options = [], ?LoggerInterface $logger = null)
    {
        $this->cache = $cache;
        $this->logger = $logger ?? new NullLogger();
        $this->options = array_merge([
            'ttl' => 3600,
            'keygen' => null,
            'actions' => []
        ], $options);
    }

    /**
     * @inheritDoc
     */
    public function process(array $ctx): ?array
    {
        $actionName = $ctx['action'] ?? null;
        
        if (!$actionName) {
            return $ctx;
        }
        
        // Check if action should be cached
        if (!empty($this->options['actions']) && !in_array($actionName, $this->options['actions'])) {
            return $ctx;
        }
        
        // Generate cache key
        $cacheKey = $this->generateCacheKey($ctx);
        
        // Try to get from cache
        $cached = $this->cache->get($cacheKey);
        
        if ($cached !== null) {
            $this->logger->debug("Using cached result for action: {$actionName}");
            return $cached;
        }
        
        // Store the cache key in context for later use
        $ctx['_cacheKey'] = $cacheKey;
        
        return $ctx;
    }

    /**
     * Generate a cache key for the context
     *
     * @param array $ctx
     * @return string
     */
    private function generateCacheKey(array $ctx): string
    {
        if (is_callable($this->options['keygen'])) {
            return call_user_func($this->options['keygen'], $ctx);
        }
        
        $actionName = $ctx['action'];
        $params = $ctx['params'] ?? [];
        
        return "action:{$actionName}:" . md5(json_encode($params));
    }

    /**
     * Cache the result of an action
     *
     * @param array $ctx
     * @param mixed $result
     */
    public function cacheResult(array $ctx, mixed $result): void
    {
        if (isset($ctx['_cacheKey'])) {
            $this->cache->set($ctx['_cacheKey'], $result, $this->options['ttl']);
            $this->logger->debug("Cached result for action: {$ctx['action']}");
        }
    }
} 