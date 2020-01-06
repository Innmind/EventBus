<?php
declare(strict_types = 1);

namespace Innmind\EventBus\ClassName;

use Innmind\Immutable\Set;

final class Inheritance implements Extractor
{
    public function __invoke(object $event): Set
    {
        $classes = Set::strings($class = \get_class($event));
        $refl = new \ReflectionClass($class);
        $interfaces = $refl->getInterfaceNames();

        foreach ($interfaces as $interface) {
            $classes = ($classes)($interface);
        }

        while (($refl = $refl->getParentClass()) !== false) {
            $classes = ($classes)($refl->getName());
        }

        return $classes;
    }
}
