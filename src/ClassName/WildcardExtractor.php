<?php
declare(strict_types = 1);

namespace Innmind\EventBus\ClassName;

use Innmind\EventBus\Exception\InvalidArgumentException;
use Innmind\Immutable\{
    SetInterface,
    Set,
    Str
};

/**
 * Transform the given event fqcn Domain\Event\Foo\Bar into the given set
 * Domain\Event\Foo\*
 * Domain\Event\*
 * Domain\*
 */
final class WildcardExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($event): SetInterface
    {
        if (!is_object($event)) {
            throw new InvalidArgumentException;
        }

        $set = new Set('string');
        $fqcn = (new Str(get_class($event)))
            ->split('\\')
            ->dropEnd(1);

        while ($fqcn->count() > 0) {
            $set = $set->add(
                $fqcn->join('\\').'\*'
            );
            $fqcn = $fqcn->dropEnd(1);
        }

        return $set;
    }
}
