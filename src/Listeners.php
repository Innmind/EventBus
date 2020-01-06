<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

final class Listeners
{
    private array $listeners;

    public function __construct(callable ...$listeners)
    {
        $this->listeners = $listeners;
    }

    public function __invoke(object $event): void
    {
        foreach ($this->listeners as $listen) {
            $listen($event);
        }
    }
}
