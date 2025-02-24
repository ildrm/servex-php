<?php

namespace Servex\Core\Transport;

interface TransportInterface
{
    public function call(string $endpoint, string $method, array $params = []): mixed;
}