<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\Immutable\MapInterface;

/**
 * @param MapInterface<string, Innmind\Immutable\SetInterface<callable>> $listeners
 */
function bootstrap(ClassName\Extractor $extractor = null): array
{
    $extractor = $extractor ?? new ClassName\Composite(
        new ClassName\Inheritance,
        new ClassName\Wildcard
    );
    $queue = new Queue;

    return [
        'bus' => static function (MapInterface $listeners) use ($extractor): EventBus {
            return new EventBus\EventBus($listeners, $extractor);
        },
        'enqueue' => new EventBus\EnqueueEventBus($queue),
        'dequeue' => static function(EventBus $bus) use ($queue): EventBus {
            return new EventBus\DequeueEventBus($bus, $queue);
        },
    ];
}
