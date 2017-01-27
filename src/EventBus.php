<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\EventBus\{
    Exception\InvalidArgumentException,
    ClassName\ExtractorInterface,
    ClassName\InheritanceExtractor
};
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    Set
};

final class EventBus implements EventBusInterface
{
    private $listeners;
    private $extractor;

    public function __construct(
        MapInterface $listeners,
        ExtractorInterface $extractor = null
    ) {
        if (
            (string) $listeners->keyType() !== 'string' ||
            (string) $listeners->valueType() !== SetInterface::class
        ) {
            throw new InvalidArgumentException;
        }

        $listeners->foreach(function(string $name, SetInterface $listeners) {
            if ((string) $listeners->type() !== 'callable') {
                throw new InvalidArgumentException;
            }
        });

        $this->listeners = $listeners;
        $this->extractor = $extractor ?? new InheritanceExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($event): EventBusInterface
    {
        if (!is_object($event)) {
            throw new InvalidArgumentException;
        }

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
