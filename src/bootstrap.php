<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\Immutable\MapInterface;

/**
 * @param MapInterface<string, Innmind\Immutable\SetInterface<callable>> $listeners
 */
function bootstrap(ClassName\ExtractorInterface $extractor = null): array
{
    $extractor = $extractor ?? new ClassName\CompositeExtractor(
        new ClassName\InheritanceExtractor,
        new ClassName\WildcardExtractor
    );
    $queue = new Queue;

    return [
        'bus' => static function (MapInterface $listeners) use ($extractor): EventBusInterface {
            return new EventBus($listeners, $extractor);
        },
        'enqueue' => new EnqueueEventBus($queue),
        'dequeue' => static function(EventBusInterface $bus) use ($queue): EventBusInterface {
            return new DequeueEventBus($bus, $queue);
        },
    ];
}
