<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\Immutable\Map;

/**
 * @param Map<string, callable> $listeners
 */
function bootstrap(ClassName\Extractor $extractor = null): array
{
    $extractor = $extractor ?? new ClassName\Composite(
        new ClassName\Inheritance,
        new ClassName\Wildcard,
    );
    $queue = new Queue;

    return [
        'bus' => static function (Map $listeners) use ($extractor): EventBus {
            return new EventBus\Map($listeners, $extractor);
        },
        'enqueue' => new EventBus\Enqueue($queue),
        'dequeue' => static function(EventBus $bus) use ($queue): EventBus {
            return new EventBus\Dequeue($bus, $queue);
        },
    ];
}
