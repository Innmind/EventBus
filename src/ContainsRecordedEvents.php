<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\Immutable\Sequence;

interface ContainsRecordedEvents
{
    /**
     * @return Sequence<object>
     */
    public function recordedEvents(): Sequence;

    public function clearEvents(): void;
}
