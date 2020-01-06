<?php
declare(strict_types = 1);

namespace Innmind\EventBus\ClassName;

use Innmind\Immutable\{
    Set,
    Str,
};
use function Innmind\Immutable\join;

/**
 * Transform the given event fqcn Domain\Event\Foo\Bar into the given set
 * Domain\Event\Foo\*
 * Domain\Event\*
 * Domain\*
 */
final class Wildcard implements Extractor
{
    public function __invoke(object $event): Set
    {
        $set = Set::strings();
        $fqcn = Str::of(\get_class($event))
            ->split('\\')
            ->dropEnd(1)
            ->mapTo('string', fn(Str $part): string => $part->toString());

        while ($fqcn->count() > 0) {
            $set = $set->add(
                join('\\', $fqcn)->append('\*')->toString(),
            );
            $fqcn = $fqcn->dropEnd(1);
        }

        return $set;
    }
}
