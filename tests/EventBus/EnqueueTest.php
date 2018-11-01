<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\EventBus;

use Innmind\EventBus\{
    EventBus\Enqueue,
    EventBus,
    Queue,
};
use PHPUnit\Framework\TestCase;

class EnqueueTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            EventBus::class,
            new Enqueue(new Queue)
        );
    }

    public function testDispatch()
    {
        $dispatch = new Enqueue($queue = new Queue);

        $event = new \stdClass;

        $this->assertSame($dispatch, $dispatch($event));
        $this->assertSame($event, $queue->dequeue());
    }
}
