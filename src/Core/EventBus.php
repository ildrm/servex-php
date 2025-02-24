<?php

namespace Servex\Core;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Psr\Log\LoggerInterface;

class EventBus
{
    private EventDispatcher $dispatcher;
    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->dispatcher = new EventDispatcher();
        $this->logger = $logger;
    }

    public function emit(string $eventName, array $data = []): void
    {
        $event = new class extends \Symfony\Contracts\EventDispatcher\Event {
            public array $data;
        };
        $event->data = $data;
        $this->logger?->info("Emitting event: {$eventName}", $data);
        $this->dispatcher->dispatch($event, $eventName);
    }

    public function on(string $eventName, callable $callback): void
    {
        $this->dispatcher->addListener($eventName, function ($event) use ($callback) {
            $callback($event->data);
        });
    }
}