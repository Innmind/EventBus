<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\EventBus\Exception\InvalidArgumentException;
use Innmind\Immutable\{
    Sequence,
    SequenceInterface
};

trait EventRecorder
{
    private $domainEvents;

    public function recordedEvents(): SequenceInterface
    {
        if ($this->domainEvents === null) {
            $this->domainEvents = new Sequence;
        }

        return $this->domainEvents;
    }

    public function clearEvents()
    {
        $this->domainEvents = new Sequence;
    }

    protected function record($event): self
    {
        if (!is_object($event)) {
            throw new InvalidArgumentException;
        }

        if ($this->domainEvents === null) {
            $this->domainEvents = new Sequence;
        }

        $this->domainEvents = $this->domainEvents->add($event);

        return $this;
    }
}
