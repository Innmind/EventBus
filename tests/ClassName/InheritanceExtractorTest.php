<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\ClassName;

use Innmind\EventBus\ClassName\{
    ExtractorInterface,
    InheritanceExtractor
};
use Fixtures\Innmind\EventBus\{
    Foo,
    Bar,
    BazInterface
};
use Innmind\Immutable\SetInterface;
use PHPUnit\Framework\TestCase;

class InheritanceExtractorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ExtractorInterface::class,
            new InheritanceExtractor
        );
    }

    public function testExecution()
    {
        $set = (new InheritanceExtractor)(new Foo);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('string', (string) $set->type());
        $this->assertCount(3, $set);
        $this->assertSame(
            [Foo::class, BazInterface::class, Bar::class],
            $set->toPrimitive()
        );
    }

    /**
     * @expectedException Innmind\EventBus\Exception\InvalidArgumentException
     */
    public function testThrowWhenEventNotAnObject()
    {
        (new InheritanceExtractor)('foo');
    }
}
