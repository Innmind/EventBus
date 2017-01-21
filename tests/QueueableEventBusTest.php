<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use Innmind\EventBus\{
    QueueableEventBus,
    EventBus,
    EventBusInterface
};
use Innmind\Immutable\{
    Map,
    SetInterface,
    Set
};

class QueueableEventBusTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $eb = new QueueableEventBus(
            new EventBus(
                (new Map('string', SetInterface::class))
                    ->put('foo', new Set('callable'))
            )
        );

        $this->assertInstanceOf(EventBusInterface::class, $eb);
    }

    public function testDispatch()
    {
        $event = new class{};
        $eventClass = get_class($event);
        $count = 0;
        $eb = new QueueableEventBus(
            new EventBus(
                (new Map('string', SetInterface::class))
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
        $this->eb = new QueueableEventBus(
            new EventBus(
                (new Map('string', SetInterface::class))
                    ->put(
                        $eventClass,
                        (new Set('callable'))
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
            )
        );

        $this->assertSame($this->eb, $this->eb->dispatch($event));
        $this->assertSame(2, $count);
        unset($this->eb);
    }

    public function testDispatchAfterAnExceptionHasBeenThrown()
    {
        $bus = $this->createMock(EventBusInterface::class);
        $bus
            ->expects($this->at(0))
            ->method('dispatch')
            ->will($this->throwException(new \Exception));
        $bus
            ->expects($this->at(1))
            ->method('dispatch');
        $queue = new QueueableEventBus($bus);

        try {
            $this->assertSame($queue, $queue->dispatch(new \stdClass));
        } catch (\Exception $e) {
            //pass
        }

        $this->assertSame($queue, $queue->dispatch(new \stdClass));
    }
}
