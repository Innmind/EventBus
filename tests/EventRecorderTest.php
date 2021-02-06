<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use Innmind\EventBus\{
    EventRecorder,
    ContainsRecordedEvents,
};
use Innmind\Immutable\Sequence;
use PHPUnit\Framework\TestCase;

class EventRecorderTest extends TestCase
{
    public function testInterface()
    {
        $recorder = new class($this) implements ContainsRecordedEvents {
            use EventRecorder;

            private $tester;

            public function __construct($tester)
            {
                $this->tester = $tester;
            }

            public function trigger()
            {
                $this->tester->assertNull(
                    $this->record(new \stdClass),
                );
            }
        };

        $recorder->trigger();
        $this->assertInstanceOf(Sequence::class, $recorder->recordedEvents());
        $this->assertSame('object', $recorder->recordedEvents()->type());
        $this->assertSame(1, $recorder->recordedEvents()->size());
        $this->assertInstanceOf('stdClass', $recorder->recordedEvents()->first());
        $this->assertSame(null, $recorder->clearEvents());
        $this->assertSame(0, $recorder->recordedEvents()->size());
    }
}
