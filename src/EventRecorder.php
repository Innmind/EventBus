<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\EventBus\Exception\InvalidArgumentException;
use Innmind\Immutable\{
    Stream,
    StreamInterface
};

trait EventRecorder
{
    private $domainEvents;

    /**
     * {@inheritdoc}
     */
    public function recordedEvents(): StreamInterface
    {
        if ($this->domainEvents === null) {
            $this->domainEvents = new Stream('object');
        }

        return $this->domainEvents;
    }

    /**
     * {@inheritdoc}
     */
    public function clearEvents(): void
    {
        $this->domainEvents = new Stream('object');
    }

    protected function record($event): self
    {
        if (!is_object($event)) {
            throw new InvalidArgumentException;
        }

        if ($this->domainEvents === null) {
            $this->domainEvents = new Stream('object');
        }

        $this->domainEvents = $this->domainEvents->add($event);

        return $this;
    }
}
