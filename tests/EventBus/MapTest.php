<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\EventBus;

use Innmind\EventBus\{
    EventBus\Map,
    EventBus as EventBusInterface,
};
use Innmind\Immutable\Map as IMap;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testInterface()
    {
        $bus = new Map(
            new IMap('string', 'callable')
        );

        $this->assertInstanceOf(EventBusInterface::class, $bus);
    }

    public function testThrowWhenInvalidListenersMapGiven()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 1 must be of type MapInterface<string, callable>');

        new Map(new IMap('string', 'array'));
    }

    public function testDispatch()
    {
        $event = new class{};
        $eventClass = get_class($event);
        $count = 0;
        $listener = new class($count) {
            private $count;

            public function __construct(&$count)
            {
                $this->count = &$count;
            }

            public function __invoke($event) {
                ++$this->count;
            }
        };
        $dispatch = new Map(
            IMap::of('string', 'callable')
                ($eventClass, $listener)
        );

        $this->assertSame($dispatch, $dispatch($event));
        $this->assertSame(1, $count);
    }

    public function testDoesntEnqueueNestedDispatchedEvents()
    {
        $event = new class{};
        $eventClass = get_class($event);
        $count = 0;
        $redispatch = new class($count, $this) {
            private $count;
            private $tester;

            public function __construct(&$count, $tester)
            {
                $this->count = &$count;
                $this->tester = $tester;
            }

            public function __invoke($event) {
                ++$this->count;
                ($this->tester->dispatch)(new \stdClass);
                $this->tester->assertSame(2, $this->count);
            }
        };
        $listener = new class($count, $this) {
            private $count;
            private $tester;

            public function __construct(&$count, $tester)
            {
                $this->count = &$count;
                $this->tester = $tester;
            }

            public function __invoke($event) {
                $this->tester->assertSame(1, $this->count);
                ++$this->count;
            }
        };
        $this->dispatch = new Map(
            IMap::of('string', 'callable')
                ($eventClass, $redispatch)
                ('stdClass', $listener)
        );

        $this->assertSame($this->dispatch, ($this->dispatch)($event));
        $this->assertSame(2, $count);
        unset($this->dispatch);
    }

    public function testDispatchWhenListeningToParentClass()
    {
        $event = new class extends \stdClass{};
        $count = 0;
        $listener = new class($count) {
            private $count;

            public function __construct(&$count)
            {
                $this->count = &$count;
            }

            public function __invoke($event) {
                ++$this->count;
            }
        };
        $dispatch = new Map(
            IMap::of('string', 'callable')
                ('stdClass', $listener)
        );

        $this->assertSame($dispatch, $dispatch($event));
        $this->assertSame(1, $count);
    }

    public function testDispatchWhenListeningToInterface()
    {
        $event = new class implements \IteratorAggregate
        {
            public function getIterator() {}
        };
        $count = 0;
        $listener = new class($count) {
            private $count;

            public function __construct(&$count)
            {
                $this->count = &$count;
            }

            public function __invoke($event) {
                ++$this->count;
            }
        };
        $dispatch = new Map(
            IMap::of('string', 'callable')
                ('IteratorAggregate', $listener)
        );

        $this->assertSame($dispatch, $dispatch($event));
        $this->assertSame(1, $count);
    }
}
