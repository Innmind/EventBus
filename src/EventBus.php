<?php
declare(strict_types = 1);

namespace Innmind\EventBus;

use Innmind\EventBus\Exception\InvalidArgumentException;
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    Sequence,
    Set
};

final class EventBus implements EventBusInterface
{
    private $listeners;
    private $eventQueue;
    private $inDispatch = false;

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
        $this->eventQueue = new Sequence;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($event): EventBusInterface
    {
        if (!is_object($event)) {
            throw new InvalidArgumentException;
        }

        if ($this->inDispatch === true) {
            $this->eventQueue = $this->eventQueue->add($event);

            return $this;
        }

        $this->inDispatch = true;
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

        $this->inDispatch = false;
        $this
            ->eventQueue
            ->foreach(function ($event) {
                $this->eventQueue = $this->eventQueue->drop(1);
                $this->dispatch($event);
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
