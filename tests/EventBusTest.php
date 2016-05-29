<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use Innmind\EventBus\{
    EventBus,
    EventBusInterface
};
use Innmind\Immutable\{
    Map,
    SetInterface,
    Set
};

class EventBusTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $eb = new EventBus(
            (new Map('string', SetInterface::class))
                ->put('foo', new Set('object'))
        );

        $this->assertInstanceOf(EventBusInterface::class, $eb);
    }

    /**
     * @expectedException Innmind\EventBus\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidListenersMapGiven()
    {
        new EventBus(new Map('string', 'array'));
    }

    /**
     * @expectedException Innmind\EventBus\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidMapOfListenersSetsGiven()
    {
        new EventBus(
            (new Map('string', SetInterface::class))
                ->put('foo', new Set('callable'))
        );
    }

    public function testDispatch()
    {
        $event = new class{};
        $eventClass = get_class($event);
        $count = 0;
        $eb = new EventBus(
            (new Map('string', SetInterface::class))
                ->put(
                    $eventClass,
                    (new Set('object'))
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

        $this->assertSame($eb, $eb->dispatch($event));
        $this->assertSame(1, $count);
    }

    public function testEnqueueNestedDispatchedEvents()
    {
        $event = new class{};
        $eventClass = get_class($event);
        $count = 0;
        $this->eb = new EventBus(
            (new Map('string', SetInterface::class))
                ->put(
                    $eventClass,
                    (new Set('object'))
                        ->add(
                            new class($count, $this)
                            {
                                private $count;
                                private $tester;

                                public function __construct(&$count, &$tester)
                                {
                                    $this->count = &$count;
                                    $this->tester = &$tester;
                                }

                                public function __invoke($event) {
                                    ++$this->count;
                                    $this->tester->eb->dispatch(new \stdClass);
                                    $this->tester->assertSame(1, $this->count);
                                }
                            }
                        )
                )
                ->put(
                    'stdClass',
                    (new Set('object'))
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

        $this->assertSame($this->eb, $this->eb->dispatch($event));
        $this->assertSame(2, $count);
        unset($this->eb);
    }

    public function testDispatchWhenListeningToParentClass()
    {
        $event = new class extends \stdClass{};
        $count = 0;
        $eb = new EventBus(
            (new Map('string', SetInterface::class))
                ->put(
                    'stdClass',
                    (new Set('object'))
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

        $this->assertSame($eb, $eb->dispatch($event));
        $this->assertSame(1, $count);
    }

    public function testDispatchWhenListeningToInterface()
    {
        $event = new class implements \IteratorAggregate
        {
            public function getIterator() {}
        };
        $count = 0;
        $eb = new EventBus(
            (new Map('string', SetInterface::class))
                ->put(
                    'IteratorAggregate',
                    (new Set('object'))
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

        $this->assertSame($eb, $eb->dispatch($event));
        $this->assertSame(1, $count);
    }
}
