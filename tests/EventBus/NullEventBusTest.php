<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\EventBus;

use Innmind\EventBus\{
    EventBus\NullEventBus,
    EventBus,
};
use PHPUnit\Framework\TestCase;

class NullEventBusTest extends TestCase
{
    public function testInterface()
    {
        $dispath = new NullEventBus;

        $this->assertInstanceOf(EventBus::class, $dispath);
        $this->assertSame($dispath, $dispath(new \stdClass));
    }
}
