<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use Innmind\EventBus\{
    EventBusInterface,
    EnqueueEventBus,
    Queue,
};
use PHPUnit\Framework\TestCase;

class EnqueueEventBusTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            EventBusInterface::class,
            new EnqueueEventBus(new Queue)
        );
    }

    public function testDispatch()
    {
        $dispatch = new EnqueueEventBus($queue = new Queue);

        $event = new \stdClass;

        $this->assertSame($dispatch, $dispatch($event));
        $this->assertSame($event, $queue->dequeue());
    }
}
