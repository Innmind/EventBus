<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\EventBus;

use Innmind\EventBus\{
    EventBus\Map,
    EventBus as EventBusInterface
};
use Innmind\Immutable\{
    Map as IMap,
    SetInterface,
    Set
};
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testInterface()
    {
        $bus = new Map(
            (new IMap('string', SetInterface::class))
                ->put('foo', new Set('callable'))
        );

        $this->assertInstanceOf(EventBusInterface::class, $bus);
    }

    /**
     * @expectedException Innmind\EventBus\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidListenersMapGiven()
    {
        new Map(new IMap('string', 'array'));
    }

    /**
     * @expectedException Innmind\EventBus\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidMapOfListenersSetsGiven()
    {
        new Map(
            (new IMap('string', SetInterface::class))
                ->put('foo', new Set('object'))
        );
    }

    public function testDispatch()
    {
        $event = new class{};
        $eventClass = get_class($event);
        $count = 0;
        $dispatch = new Map(
            (new IMap('string', SetInterface::class))
                ->put(
                    $eventClass,
                    (new Set('callable'))
                        ->add(
                            new class($count)
                            {
                                private $count;

                                public function __construct(&$count)
                                {
                                    $this->count = &$count;
                                }

                                public function __invoke($event) {
                                    ++$this->count;
                                }
                            }
                        )
                )
        );

        $this->assertSame($dispatch, $dispatch($event));
        $this->assertSame(1, $count);
    }

    public function testDoesntEnqueueNestedDispatchedEvents()
    {
        $event = new class{};
        $eventClass = get_class($event);
        $count = 0;
        $this->dispatch = new Map(
            (new IMap('string', SetInterface::class))
                ->put(
                    $eventClass,
                    (new Set('callable'))
                        ->add(
                            new class($count, $this)
                            {
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
                            }
                        )
                )
                ->put(
                    'stdClass',
                    (new Set('callable'))
                        ->add(
                            new class($count, $this)
                            {
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
                            }
                        )
                )
        );

        $this->assertSame($this->dispatch, ($this->dispatch)($event));
        $this->assertSame(2, $count);
        unset($this->dispatch);
    }

    public function testDispatchWhenListeningToParentClass()
    {
        $event = new class extends \stdClass{};
        $count = 0;
        $dispatch = new Map(
            (new IMap('string', SetInterface::class))
                ->put(
                    'stdClass',
                    (new Set('callable'))
                        ->add(
                            new class($count)
                            {
                                private $count;

                                public function __construct(&$count)
                                {
                                    $this->count = &$count;
                                }

                                public function __invoke($event) {
                                    ++$this->count;
                                }
                            }
                        )
                )
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
        $dispatch = new Map(
            (new IMap('string', SetInterface::class))
                ->put(
                    'IteratorAggregate',
                    (new Set('callable'))
                        ->add(
                            new class($count)
                            {
                                private $count;

                                public function __construct(&$count)
                                {
                                    $this->count = &$count;
                                }

                                public function __invoke($event) {
                                    ++$this->count;
                                }
                            }
                        )
                )
        );

        $this->assertSame($dispatch, $dispatch($event));
        $this->assertSame(1, $count);
    }
}
