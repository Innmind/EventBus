<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

final class EnqueueEventBus implements EventBusInterface
{
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($event): EventBusInterface
    {
        $this->queue->enqueue($event);

        return $this;
    }
}
