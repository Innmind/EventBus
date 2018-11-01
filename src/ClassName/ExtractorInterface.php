<?php
declare(strict_types = 1);

namespace Innmind\EventBus\ClassName;

use Innmind\Immutable\SetInterface;

interface ExtractorInterface
{
    /**
     * @return SetInterface<string>
     */
    public function __invoke(object $event): SetInterface;
}
