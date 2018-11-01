<?php
declare(strict_types = 1);

namespace Innmind\EventBus\ClassName;

use Innmind\EventBus\Exception\InvalidArgumentException;
use Innmind\Immutable\{
    SetInterface,
    Set,
};

final class Inheritance implements Extractor
{
    public function __invoke(object $event): SetInterface
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
