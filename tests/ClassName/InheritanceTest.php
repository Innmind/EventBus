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
use Innmind\Immutable\Set;
use function Innmind\Immutable\unwrap;
use PHPUnit\Framework\TestCase;

class InheritanceTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Extractor::class,
            new Inheritance,
        );
    }

    public function testExecution()
    {
        $set = (new Inheritance)(new Foo);

        $this->assertInstanceOf(Set::class, $set);
        $this->assertSame('string', $set->type());
        $this->assertCount(3, $set);
        $this->assertSame(
            [Foo::class, BazInterface::class, Bar::class],
            unwrap($set),
        );
    }
}
