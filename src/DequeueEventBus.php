<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

final class DequeueEventBus implements EventBusInterface
{
    private $dispatch;
    private $queue;

    public function __construct(EventBusInterface $dispatch, Queue $queue)
    {
        $this->dispatch = $dispatch;
        $this->queue = $queue;
    }

    public function __invoke(object $event): EventBusInterface
    {
        ($this->dispatch)($event);

        while ($this->queue->valid()) {
            ($this->dispatch)($this->queue->dequeue());
        }

        return $this;
    }
}
