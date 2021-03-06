<?php
declare(strict_types = 1);

namespace Innmind\EventBus\EventBus;

use Innmind\EventBus\{
    EventBus,
    Queue,
};

final class Enqueue implements EventBus
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function __invoke(object $event): void
    {
        $this->queue->enqueue($event);
    }
}
