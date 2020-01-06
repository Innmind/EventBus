<?php
declare(strict_types = 1);

namespace Innmind\EventBus\ClassName;

use Innmind\Immutable\Set;

interface Extractor
{
    /**
     * @return Set<string>
     */
    public function __invoke(object $event): Set;
}
