<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\Immutable\SequenceInterface;

interface ContainsRecordedEventsInterface
{
    /**
     * @return SequenceInterface
     */
    public function recordedEvents(): SequenceInterface;

    /**
     * @return void
     */
    public function clearEvents();
}