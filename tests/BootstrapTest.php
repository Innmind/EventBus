<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use function Innmind\EventBus\bootstrap;
use Innmind\EventBus\{
    EventBus\EventBus,
    EventBus\DequeueEventBus,
    EventBus\EnqueueEventBus,
};
use Innmind\Immutable\{
    Map,
    SetInterface,
    Set,
};
use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    public function testBootstrap()
    {
        $buses = bootstrap();
        $bus = $buses['bus'];
        $enqueue = $buses['enqueue'];
        $dequeue = $buses['dequeue'];

        $this->assertInternalType('callable', $bus);
        $this->assertInstanceOf(
            EventBus::class,
            $bus(new Map('string', SetInterface::class))
        );
        $this->assertInstanceOf(EnqueueEventBus::class, $enqueue);
        $this->assertInternalType('callable', $dequeue);
        $this->assertInstanceOf(
            DequeueEventBus::class,
            $dequeue($bus(new Map('string', SetInterface::class)))
        );
    }

    public function testQueue()
    {
        $buses = bootstrap();
        $bus = $buses['bus'];
        $enqueue = $buses['enqueue'];
        $dequeue = $buses['dequeue'];

        $called = 0;
        $listeners = (new Map('string', SetInterface::class))
            ->put('stdClass', Set::of(
                'callable',
                function() use ($enqueue): void {
                    $enqueue($this);
                }
            ))
            ->put(get_class($this), Set::of(
                'callable',
                static function() use (&$called): void {
                    ++$called;
                }
            ));

        $dispatch = $dequeue($bus($listeners));
        $this->assertSame($dispatch, $dispatch(new \stdClass));
        $this->assertSame(1, $called);
    }
}
