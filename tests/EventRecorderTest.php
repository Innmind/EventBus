<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use Innmind\EventBus\{
    EventRecorder,
    ContainsRecordedEventsInterface
};
use Innmind\Immutable\StreamInterface;
use PHPUnit\Framework\TestCase;

class EventRecorderTest extends TestCase
{
    public function testInterface()
    {
        $recorder = new class($this) implements ContainsRecordedEventsInterface
        {
            use EventRecorder;

            private $tester;

            public function __construct($tester)
            {
                $this->tester = $tester;
            }

            public function trigger()
            {
                $this->tester->assertSame(
                    $this,
                    $this->record(new \stdClass)
                );
            }
        };

        $recorder->trigger();
        $this->assertInstanceOf(StreamInterface::class, $recorder->recordedEvents());
        $this->assertSame('object', (string) $recorder->recordedEvents()->type());
        $this->assertSame(1, $recorder->recordedEvents()->size());
        $this->assertInstanceOf('stdClass', $recorder->recordedEvents()->current());
        $this->assertSame(null, $recorder->clearEvents());
        $this->assertSame(0, $recorder->recordedEvents()->size());
    }

    /**
     * @expectedException Innmind\EventBus\Exception\InvalidArgumentException
     */
    public function testThrowWhenNotUsingAnObjectWhenRecordingAnEvent()
    {
        $recorder = new class implements ContainsRecordedEventsInterface
        {
            use EventRecorder;

            public function trigger()
            {
                $this->record('foo');
            }
        };

        $recorder->trigger();
    }
}
