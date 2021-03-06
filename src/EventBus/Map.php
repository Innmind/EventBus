<?php
declare(strict_types = 1);

namespace Innmind\EventBus\EventBus;

use Innmind\EventBus\{
    EventBus as EventBusInterface,
    ClassName\Extractor,
    ClassName\Inheritance,
};
use Innmind\Immutable\Map as IMap;
use function Innmind\Immutable\assertMap;

final class Map implements EventBusInterface
{
    /** @var IMap<string, callable> */
    private IMap $listeners;
    private Extractor $extractor;

    /** @var IMap<string, callable> $listeners */
    public function __construct(
        IMap $listeners,
        Extractor $extractor = null
    ) {
        assertMap('string', 'callable', $listeners, 1);

        /** @var IMap<string, callable> */
        $this->listeners = $listeners;
        $this->extractor = $extractor ?? new Inheritance;
    }

    public function __invoke(object $event): void
    {
        $keys = ($this->extractor)($event);
        $keys->foreach(function(string $class) use ($event): void {
            if ($this->listeners->contains($class)) {
                $listen = $this->listeners->get($class);

                $listen($event);
            }
        });
    }
}
