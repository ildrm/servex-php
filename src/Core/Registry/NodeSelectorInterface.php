<?php

namespace Servex\Core\Registry;

/**
 * Node Selector Interface
 * Interface for node selection strategies
 */
interface NodeSelectorInterface
{
    /**
     * Select a node from available nodes
     *
     * @param array $nodes
     * @param string $actionName
     * @return string|null
     */
    public function select(array $nodes, string $actionName): ?string;
} 