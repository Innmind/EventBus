<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\ClassName;

use Innmind\EventBus\ClassName\{
    ExtractorInterface,
    CompositeExtractor
};
use Innmind\Immutable\{
    SetInterface,
    Set
};

class CompositeExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ExtractorInterface::class,
            new CompositeExtractor
        );
    }

    public function testExecution()
    {
        $extractor = new CompositeExtractor(
            $mock1 = $this->createMock(ExtractorInterface::class),
            $mock2 = $this->createMock(ExtractorInterface::class)
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
