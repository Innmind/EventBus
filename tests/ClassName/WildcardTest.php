<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\ClassName;

use Innmind\EventBus\ClassName\{
    Wildcard,
    Extractor,
};
use Fixtures\Innmind\EventBus\Foo;
use Innmind\Immutable\SetInterface;
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

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('string', (string) $set->type());
        $this->assertCount(3, $set);
        $this->assertSame(
            ['Fixtures\Innmind\EventBus\*', 'Fixtures\Innmind\*', 'Fixtures\*'],
            $set->toPrimitive()
        );
    }
}
