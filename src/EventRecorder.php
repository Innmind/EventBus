<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\Immutable\{
    Stream,
    StreamInterface,
};

trait EventRecorder
{
    private $domainEvents;

    /**
     * {@inheritdoc}
     */
    public function recordedEvents(): StreamInterface
    {
        return $this->domainEvents ?? $this->domainEvents = Stream::of('object');
    }

    /**
     * {@inheritdoc}
     */
    public function clearEvents(): void
    {
        $this->domainEvents = new Stream('object');
    }

    protected function record(object $event): self
    {
        $this->domainEvents = $this->recordedEvents()->add($event);

        return $this;
    }
}
