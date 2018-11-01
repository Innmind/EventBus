<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\ClassName;

use Innmind\EventBus\ClassName\{
    Composite,
    Extractor,
};
use Innmind\Immutable\{
    SetInterface,
    Set,
};
use PHPUnit\Framework\TestCase;

class CompositeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Extractor::class,
            new Composite
        );
    }

    public function testExecution()
    {
        $extractor = new Composite(
            $mock1 = $this->createMock(Extractor::class),
            $mock2 = $this->createMock(Extractor::class)
        );
        $event = new class{};
        $mock1
            ->expects($this->once())
            ->method('__invoke')
            ->with($event)
            ->willReturn((new Set('string'))->add('foo')->add('bar'));
        $mock2
            ->expects($this->once())
            ->method('__invoke')
            ->with($event)
            ->willReturn((new Set('string'))->add('bar')->add('baz'));

        $set = $extractor($event);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('string', (string) $set->type());
        $this->assertCount(3, $set);
        $this->assertSame(['foo', 'bar', 'baz'], $set->toPrimitive());
    }
}
