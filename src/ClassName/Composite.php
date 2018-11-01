<?php
declare(strict_types = 1);

namespace Innmind\EventBus\ClassName;

use Innmind\Immutable\{
    SetInterface,
    Set,
};

final class Composite implements Extractor
{
    private $extractors;

    public function __construct(Extractor ...$extractors)
    {
        $this->extractors = $extractors;
    }

    public function __invoke(object $event): SetInterface
    {
        $set = new Set('string');

        foreach ($this->extractors as $extractor) {
            $set = $set->merge($extractor($event));
        }

        return $set;
    }
}
