<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

final class DequeueEventBus implements EventBusInterface
{
    private $bus;
    private $queue;

    public function __construct(EventBusInterface $bus, Queue $queue)
    {
        $this->bus = $bus;
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($event): EventBusInterface
    {
        $this->bus->dispatch($event);

        while ($this->queue->valid()) {
            $this->bus->dispatch($this->queue->dequeue());
        }

        return $this;
    }
}
