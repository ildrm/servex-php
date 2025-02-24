<?php

namespace Servex\Core;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $entries = [];

    public function get($id)
    {
        if (!isset($this->entries[$id])) {
            throw new \InvalidArgumentException("Service {$id} not found in container.");
        }
        return $this->entries[$id]();
    }

    public function has($id): bool
    {
        return isset($this->entries[$id]);
    }

    public function set(string $id, callable $factory): void
    {
        $this->entries[$id] = $factory;
    }
}