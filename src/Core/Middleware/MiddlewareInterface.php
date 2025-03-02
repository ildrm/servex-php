<?php

namespace Servex\Core\Middleware;

/**
 * Middleware Interface
 * Interface for middleware handlers
 */
interface MiddlewareInterface
{
    /**
     * Process the context and return the modified context
     *
     * @param array $ctx
     * @return array|null
     */
    public function process(array $ctx): ?array;
}