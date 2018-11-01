<?php
declare(strict_types = 1);

namespace Innmind\EventBus\EventBus;

use Innmind\EventBus\{
    EventBus,
    Queue,
};

final class EnqueueEventBus implements EventBus
{
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function __invoke(object $event): EventBus
    {
        $this->queue->enqueue($event);

        return $this;
    }
}
