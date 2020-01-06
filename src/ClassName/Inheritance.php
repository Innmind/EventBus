<?php
declare(strict_types = 1);

namespace Innmind\EventBus\ClassName;

use Innmind\Immutable\{
    SetInterface,
    Set,
};

final class Inheritance implements Extractor
{
    public function __invoke(object $event): SetInterface
    {
        $classes = Set::of('string', $class = \get_class($event));
        $refl = new \ReflectionClass($class);
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
