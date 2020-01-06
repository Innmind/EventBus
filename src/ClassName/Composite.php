<?php
declare(strict_types = 1);

namespace Innmind\EventBus\ClassName;

use Innmind\Immutable\Set;

final class Composite implements Extractor
{
    /** @var list<Extractor> */
    private array $extractors;

    public function __construct(Extractor ...$extractors)
    {
        $this->extractors = $extractors;
    }

    public function __invoke(object $event): Set
    {
        $set = Set::strings();

        foreach ($this->extractors as $extractor) {
            $set = $set->merge($extractor($event));
        }

        return $set;
    }
}
