<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\ClassName;

use Innmind\EventBus\ClassName\{
    Composite,
    Extractor,
};
use Innmind\Immutable\Set;
use function Innmind\Immutable\unwrap;
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
        $event = new class {
        };
        $mock1
            ->expects($this->once())
            ->method('__invoke')
            ->with($event)
            ->willReturn(Set::strings('foo', 'bar'));
        $mock2
            ->expects($this->once())
            ->method('__invoke')
            ->with($event)
            ->willReturn(Set::strings('bar', 'baz'));

        $set = $extractor($event);

        $this->assertInstanceOf(Set::class, $set);
        $this->assertSame('string', $set->type());
        $this->assertCount(3, $set);
        $this->assertSame(['foo', 'bar', 'baz'], unwrap($set));
    }
}
