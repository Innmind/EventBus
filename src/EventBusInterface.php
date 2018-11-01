<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

interface EventBusInterface
{
    public function dispatch(object $event): self;
}
