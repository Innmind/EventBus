<?php
declare(strict_types = 1);

namespace Innmind\EventBus\ClassName;

use Innmind\Immutable\SetInterface;

interface ExtractorInterface
{
    /**
     * @param object $event
     *
     * @return SetInterface<string>
     */
    public function __invoke($event): SetInterface;
}
