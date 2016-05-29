<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

interface EventBusInterface
{
    /**
     * @param object $event
     */
    public function dispatch($event): self;
}
