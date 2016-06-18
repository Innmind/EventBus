<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\EventBus\Exception\InvalidArgumentException;

final class NullEventBus implements EventBusInterface
{
    /**
     * {@inheritdoc}
     */
    public function dispatch($event): EventBusInterface
    {
        if (!is_object($event)) {
            throw new InvalidArgumentException;
        }

        return $this;
    }
}
