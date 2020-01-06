<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use function Innmind\EventBus\bootstrap;
use Innmind\EventBus\{
    EventBus\Map,
    EventBus\Dequeue,
    EventBus\Enqueue,
};
use Innmind\Immutable\Map as IMap;
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
            $bus(IMap::of('string', 'callable'))
        );
        $this->assertInstanceOf(Enqueue::class, $enqueue);
        $this->assertInternalType('callable', $dequeue);
        $this->assertInstanceOf(
            Dequeue::class,
            $dequeue($bus(IMap::of('string', 'callable')))
        );
    }

    public function testQueue()
    {
        $buses = bootstrap();
        $bus = $buses['bus'];
        $enqueue = $buses['enqueue'];
        $dequeue = $buses['dequeue'];

        $called = 0;
        $listeners = IMap::of('string', 'callable')
            ('stdClass', function() use ($enqueue): void {
                $enqueue($this);
            })
            (get_class($this), static function() use (&$called): void {
                ++$called;
            });

        $dispatch = $dequeue($bus($listeners));
        $this->assertNull($dispatch(new \stdClass));
        $this->assertSame(1, $called);
    }
}
