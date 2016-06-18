<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\EventBus\Exception\InvalidArgumentException;
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    Set
};

final class EventBus implements EventBusInterface
{
    private $listeners;

    public function __construct(MapInterface $listeners)
    {
        if (
            (string) $listeners->keyType() !== 'string' ||
            (string) $listeners->valueType() !== SetInterface::class
        ) {
            throw new InvalidArgumentException;
        }

        $listeners->foreach(function (string $name, SetInterface $listeners) {
            if ((string) $listeners->type() !== 'callable') {
                throw new InvalidArgumentException;
            }
        });

        $this->listeners = $listeners;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($event): EventBusInterface
    {
        if (!is_object($event)) {
            throw new InvalidArgumentException;
        }

        $classes = $this->classesFor($event);
        $classes->foreach(function (string $class) use ($event) {
            if ($this->listeners->contains($class)) {
                $this
                    ->listeners
                    ->get($class)
                    ->foreach(function ($listener) use ($event) {
                        $listener($event);
                    });
            }
        });

        return $this;
    }

    /**
     * Determine all parent classes and interface the event implements
     *
     * @param object $event
     *
     * @return SetInterface<string>
     */
    private function classesFor($event): SetInterface
    {
        $classes = (new Set('string'))->add(get_class($event));
        $refl = new \ReflectionClass($classes->current());
        $interfaces = $refl->getInterfaceNames();

        foreach ($interfaces as $interface) {
            $classes = $classes->add($interface);
        }

        while (($refl = $refl->getParentClass()) !== false) {
            $classes = $classes->add($refl->getName());
        }

        return $classes;
    }
}
