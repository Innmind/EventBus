<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\Immutable\StreamInterface;

interface ContainsRecordedEvents
{
    /**
     * @return StreamInterface<object>
     */
    public function recordedEvents(): StreamInterface;

    /**
     * @return void
     */
    public function clearEvents(): void;
}