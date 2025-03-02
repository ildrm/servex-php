<?php

namespace Servex\Core\Transport;

interface TransportInterface
{
    /**
     * Call a remote service action
     *
     * @param string $actionName
     * @param array $params
     * @return mixed
     */
    public function call(string $actionName, array $params = []): mixed;
    
    /**
     * Connect to the transport system
     *
     * @return void
     */
    public function connect(): void;
    
    /**
     * Disconnect from the transport system
     *
     * @return void
     */
    public function disconnect(): void;
    
    /**
     * Broadcast a message to all nodes
     *
     * @param string $topic
     * @param mixed $data
     * @return void
     */
    public function broadcast(string $topic, mixed $data): void;
    
    /**
     * Subscribe to a topic
     *
     * @param string $topic
     * @param callable $handler
     * @return void
     */
    public function subscribe(string $topic, callable $handler): void;
}