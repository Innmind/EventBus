<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\Immutable\Sequence;

final class QueueableEventBus implements EventBusInterface
{
    private $eventBus;
    private $inDispatch = false;
    private $eventQueue;

    public function __construct(EventBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
        $this->eventQueue = new Sequence;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($event): EventBusInterface
    {
        if ($this->inDispatch === true) {
            $this->eventQueue = $this->eventQueue->add($event);

            return $this;
        }

        $this->inDispatch = true;

        try {
            $this->eventBus->dispatch($event);
        } catch (\Throwable $e) {
            $this->eventQueue = new Sequence;
            $this->inDispatch = false;
            throw $e;
        }

        $this->inDispatch = false;
        $this
            ->eventQueue
            ->foreach(function($event) {
                $this->eventQueue = $this->eventQueue->drop(1);
                $this->dispatch($event);
            });

        return $this;
    }
}
