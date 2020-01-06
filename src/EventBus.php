<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

interface EventBus
{
    public function __invoke(object $event): void;
}
