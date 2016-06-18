<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

interface EventBusInterface
{
    /**
     * @throws InvalidArgumentException If the event is not an object
     *
     * @param object $event
     */
    public function dispatch($event): self;
}
