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
        $eb = new NullEventBus;

        $this->assertInstanceOf(EventBusInterface::class, $eb);
        $this->assertSame($eb, $eb->dispatch(new \stdClass));
    }

    /**
     * @expectedException Innmind\EventBus\Exception\InvalidArgumentException
     */
    public function testThrowIfEventIsNotAnObject()
    {
        (new NullEventBus)->dispatch([]);
    }
}
