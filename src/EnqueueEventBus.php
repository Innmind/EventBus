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

    public function __invoke(object $event): EventBusInterface
    {
        $this->queue->enqueue($event);

        return $this;
    }
}
