<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\EventBus;

use Innmind\EventBus\{
    EventBus\Dequeue,
    EventBus,
    Queue,
};
use PHPUnit\Framework\TestCase;

class DequeueTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            EventBus::class,
            new Dequeue(
                $this->createMock(EventBus::class),
                new Queue,
            )
        );
    }

    public function testDispatchWithNoEnqueue()
    {
        $dispatch = new Dequeue(
            $inner = $this->createMock(EventBus::class),
            new Queue,
        );
        $event = new \stdClass;
        $inner
            ->expects($this->once())
            ->method('__invoke')
            ->with($event);

        $this->assertNull($dispatch($event));
    }

    public function testDispatchWithEnqueue()
    {
        $dispatch = new Dequeue(
            $inner = $this->createMock(EventBus::class),
            $queue = new Queue,
        );
        $event = new \stdClass;
        $inner
            ->expects($this->exactly(2))
            ->method('__invoke')
            ->withConsecutive(
                [$this->callback(static function($event) use ($queue): bool {
                    $queue->enqueue($event);

                    return true;
                })],
                [$event],
            );

        $this->assertNull($dispatch($event));
    }
}
