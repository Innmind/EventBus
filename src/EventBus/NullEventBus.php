<?php
declare(strict_types = 1);

namespace Innmind\EventBus\EventBus;

use Innmind\EventBus\EventBus;

final class NullEventBus implements EventBus
{
    public function __invoke(object $event): EventBus
    {
        return $this;
    }
}
