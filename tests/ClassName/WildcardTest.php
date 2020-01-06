<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\ClassName;

use Innmind\EventBus\ClassName\{
    Wildcard,
    Extractor,
};
use Fixtures\Innmind\EventBus\Foo;
use Innmind\Immutable\Set;
use function Innmind\Immutable\unwrap;
use PHPUnit\Framework\TestCase;

class WildcardTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Extractor::class,
            new Wildcard
        );
    }

    public function testExecution()
    {
        $set = (new Wildcard)(new Foo);

        $this->assertInstanceOf(Set::class, $set);
        $this->assertSame('string', $set->type());
        $this->assertCount(3, $set);
        $this->assertSame(
            ['Fixtures\Innmind\EventBus\*', 'Fixtures\Innmind\*', 'Fixtures\*'],
            unwrap($set),
        );
    }
}
