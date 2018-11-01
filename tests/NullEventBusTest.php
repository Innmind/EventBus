<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use Innmind\EventBus\{
    NullEventBus,
    EventBusInterface
};
use PHPUnit\Framework\TestCase;

class NullEventBusTest extends TestCase
{
    public function testInterface()
    {
        $dispath = new NullEventBus;

        $this->assertInstanceOf(EventBusInterface::class, $dispath);
        $this->assertSame($dispath, $dispath(new \stdClass));
    }
}
