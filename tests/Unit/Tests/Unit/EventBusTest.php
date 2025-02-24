<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Servex\Core\EventBus;

class EventBusTest extends TestCase
{
    private EventBus $eventBus;

    protected function setUp(): void
    {
        $this->eventBus = new EventBus();
    }

    public function testEmitAndListenEvent(): void
    {
        $eventName = 'test.event';
        $data = ['key' => 'value'];
        $receivedData = null;

        $this->eventBus->on($eventName, function ($eventData) use (&$receivedData) {
            $receivedData = $eventData;
        });

        $this->eventBus->emit($eventName, $data);

        $this->assertEquals($data, $receivedData);
    }
}