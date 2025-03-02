<?php

namespace Servex\Core\Registry;

/**
 * Random Strategy
 * Selects nodes randomly
 */
class RandomStrategy implements NodeSelectorInterface
{
    /**
     * @inheritDoc
     */
    public function select(array $nodes, string $actionName): ?string
    {
        if (empty($nodes)) {
            return null;
        }
        
        $nodeIDs = array_values($nodes);
        $index = array_rand($nodeIDs);
        
        return $nodeIDs[$index];
    }
} 