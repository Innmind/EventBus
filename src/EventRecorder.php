<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\Immutable\Sequence;

trait EventRecorder
{
    private ?Sequence $recordedEvents = null;

    public function recordedEvents(): Sequence
    {
        return $this->recordedEvents ??= Sequence::objects();
    }

    public function clearEvents(): void
    {
        $this->recordedEvents = Sequence::objects();
    }

    protected function record(object $event): void
    {
        $this->recordedEvents = ($this->recordedEvents())($event);
    }
}
