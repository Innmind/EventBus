<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\ClassName;

use Innmind\EventBus\ClassName\{
    Inheritance,
    Extractor,
};
use Fixtures\Innmind\EventBus\{
    Foo,
    Bar,
    BazInterface,
};
use Innmind\Immutable\SetInterface;
use PHPUnit\Framework\TestCase;

class InheritanceTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Extractor::class,
            new Inheritance
        );
    }

    public function testExecution()
    {
        $set = (new Inheritance)(new Foo);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('string', (string) $set->type());
        $this->assertCount(3, $set);
        $this->assertSame(
            [Foo::class, BazInterface::class, Bar::class],
            $set->toPrimitive()
        );
    }
}
