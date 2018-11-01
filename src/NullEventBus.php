<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\EventBus\Exception\InvalidArgumentException;

final class NullEventBus implements EventBusInterface
{
    public function dispatch(object $event): EventBusInterface
    {
        return $this;
    }
}
