<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use function Innmind\EventBus\bootstrap;
use Innmind\EventBus\{
    EventBus\Map,
    EventBus\Dequeue,
    EventBus\Enqueue,
};
use Innmind\Immutable\{
    Map as IMap,
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
            Map::class,
            $bus(new IMap('string', SetInterface::class))
        );
        $this->assertInstanceOf(Enqueue::class, $enqueue);
        $this->assertInternalType('callable', $dequeue);
        $this->assertInstanceOf(
            Dequeue::class,
            $dequeue($bus(new IMap('string', SetInterface::class)))
        );
    }

    public function testQueue()
    {
        $buses = bootstrap();
        $bus = $buses['bus'];
        $enqueue = $buses['enqueue'];
        $dequeue = $buses['dequeue'];

        $called = 0;
        $listeners = (new IMap('string', SetInterface::class))
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
