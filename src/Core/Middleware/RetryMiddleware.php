<?php

namespace Servex\Core\Middleware;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Retry Middleware
 * Retries failed actions
 */
class RetryMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;
    private array $options;

    /**
     * RetryMiddleware constructor
     *
     * @param array $options
     * @param LoggerInterface|null $logger
     */
    public function __construct(array $options = [], ?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new \Psr\Log\NullLogger();
        $this->options = array_merge([
            'retries' => 5,
            'delay' => 100,
            'maxDelay' => 2000,
            'factor' => 2,
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
        
        // Check if action should be retried
        if (!empty($this->options['actions']) && !in_array($actionName, $this->options['actions'])) {
            return $ctx;
        }
        
        // Add retry information to context
        $ctx['_retry'] = [
            'retries' => $this->options['retries'],
            'delay' => $this->options['delay'],
            'maxDelay' => $this->options['maxDelay'],
            'factor' => $this->options['factor'],
            'currentRetry' => 0
        ];
        
        return $ctx;
    }

    /**
     * Handle error and retry if needed
     *
     * @param array $ctx
     * @param \Throwable $error
     * @param callable $next
     * @return mixed
     * @throws \Throwable
     */
    public function handleError(array $ctx, \Throwable $error, callable $next): mixed
    {
        if (!isset($ctx['_retry'])) {
            throw $error;
        }
        
        $retry = $ctx['_retry'];
        
        if ($retry['currentRetry'] >= $retry['retries']) {
            $this->logger->error("Retry limit reached for action: {$ctx['action']}");
            throw $error;
        }
        
        // Calculate delay
        $delay = min($retry['delay'] * pow($retry['factor'], $retry['currentRetry']), $retry['maxDelay']);
        
        $this->logger->info("Retrying action: {$ctx['action']} (attempt " . ($retry['currentRetry'] + 1) . "/{$retry['retries']}) after {$delay} milliseconds");
        
        // Sleep for the delay
        usleep($delay * 1000);
        
        // Increment retry counter
        $ctx['_retry']['currentRetry']++;
        
        // Try again
        return $next($ctx);
    }
}
