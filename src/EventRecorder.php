<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\Immutable\{
    Stream,
    StreamInterface,
};

trait EventRecorder
{
    private StreamInterface $recordedEvents;

    /**
     * {@inheritdoc}
     */
    public function recordedEvents(): StreamInterface
    {
        return $this->recordedEvents ?? $this->recordedEvents = Stream::of('object');
    }

    /**
     * {@inheritdoc}
     */
    public function clearEvents(): void
    {
        $this->recordedEvents = new Stream('object');
    }

    protected function record(object $event): self
    {
        $this->recordedEvents = $this->recordedEvents()->add($event);

        return $this;
    }
}
