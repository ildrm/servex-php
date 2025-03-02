<?php

namespace Servex\Core\Registry;

/**
 * Round Robin Strategy
 * Selects nodes in a round-robin fashion
 */
class RoundRobinStrategy implements NodeSelectorInterface
{
    private array $counters = [];

    /**
     * @inheritDoc
     */
    public function select(array $nodes, string $actionName): ?string
    {
        if (empty($nodes)) {
            return null;
        }
        
        if (!isset($this->counters[$actionName])) {
            $this->counters[$actionName] = 0;
        }
        
        $nodeIDs = array_values($nodes);
        $index = $this->counters[$actionName] % count($nodeIDs);
        $this->counters[$actionName]++;
        
        return $nodeIDs[$index];
    }
} 