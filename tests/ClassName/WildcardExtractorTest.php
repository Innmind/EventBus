<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus\ClassName;

use Innmind\EventBus\ClassName\{
    Extractor,
    WildcardExtractor
};
use Fixtures\Innmind\EventBus\Foo;
use Innmind\Immutable\SetInterface;
use PHPUnit\Framework\TestCase;

class WildcardExtractorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Extractor::class,
            new WildcardExtractor
        );
    }

    public function testExecution()
    {
        $set = (new WildcardExtractor)(new Foo);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('string', (string) $set->type());
        $this->assertCount(3, $set);
        $this->assertSame(
            ['Fixtures\Innmind\EventBus\*', 'Fixtures\Innmind\*', 'Fixtures\*'],
            $set->toPrimitive()
        );
    }
}
