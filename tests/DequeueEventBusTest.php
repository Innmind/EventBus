<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use Innmind\EventBus\{
    EventBusInterface,
    DequeueEventBus,
    Queue,
};
use PHPUnit\Framework\TestCase;

class DequeueEventBusTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            EventBusInterface::class,
            new DequeueEventBus(
                $this->createMock(EventBusInterface::class),
                new Queue
            )
        );
    }

    public function testDispatchWithNoEnqueue()
    {
        $bus = new DequeueEventBus(
            $inner = $this->createMock(EventBusInterface::class),
            new Queue
        );
        $event = new \stdClass;
        $inner
            ->expects($this->once())
            ->method('dispatch')
            ->with($event);

        $this->assertSame($bus, $bus->dispatch($event));
    }

    public function testDispatchWithEnqueue()
    {
        $bus = new DequeueEventBus(
            $inner = $this->createMock(EventBusInterface::class),
            $queue = new Queue
        );
        $event = new \stdClass;
        $inner
            ->expects($this->at(0))
            ->method('dispatch')
            ->with($this->callback(function($event) use ($queue): bool {
                $queue->enqueue($event);

                return true;
            }));
        $inner
            ->expects($this->at(1))
            ->method('dispatch')
            ->with($event);

        $this->assertSame($bus, $bus->dispatch($event));
    }
}
