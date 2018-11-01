<?php
declare(strict_types = 1);

namespace Innmind\EventBus\EventBus;

use Innmind\EventBus\{
    EventBus,
    Queue,
};

final class Dequeue implements EventBus
{
    private $dispatch;
    private $queue;

    public function __construct(EventBus $dispatch, Queue $queue)
    {
        $this->dispatch = $dispatch;
        $this->queue = $queue;
    }

    public function __invoke(object $event): EventBus
    {
        ($this->dispatch)($event);

        while ($this->queue->valid()) {
            ($this->dispatch)($this->queue->dequeue());
        }

        return $this;
    }
}
