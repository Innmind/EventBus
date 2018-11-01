<?php
declare(strict_types = 1);

namespace Innmind\EventBus\EventBus;

use Innmind\EventBus\{
    EventBus as EventBusInterface,
    ClassName\Extractor,
    ClassName\Inheritance,
};
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    Set,
};

final class Map implements EventBusInterface
{
    private $listeners;
    private $extractor;

    public function __construct(
        MapInterface $listeners,
        Extractor $extractor = null
    ) {
        if (
            (string) $listeners->keyType() !== 'string' ||
            (string) $listeners->valueType() !== SetInterface::class
        ) {
            throw new \TypeError('Argument 1 must be of type MapInterface<string, SetInterface<callable>>');
        }

        $listeners->foreach(function(string $name, SetInterface $listeners) {
            if ((string) $listeners->type() !== 'callable') {
                throw new \TypeError('Argument 1 must be of type MapInterface<string, SetInterface<callable>>');
            }
        });

        $this->listeners = $listeners;
        $this->extractor = $extractor ?? new Inheritance;
    }

    public function __invoke(object $event): EventBusInterface
    {
        $keys = ($this->extractor)($event);
        $keys->foreach(function(string $class) use ($event) {
            if ($this->listeners->contains($class)) {
                $this
                    ->listeners
                    ->get($class)
                    ->foreach(function($listener) use ($event) {
                        $listener($event);
                    });
            }
        });

        return $this;
    }
}
