<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use Innmind\Compose\{
    ContainerBuilder\ContainerBuilder,
    Loader\Yaml
};
use Innmind\Url\Path;
use Innmind\Immutable\{
    Map,
    SetInterface,
    Set
};
use PHPUnit\Framework\TestCase;
use Fixtures\Innmind\EventBus\Foo;

class ContainerTest extends TestCase
{
    public function testBuild()
    {
        $wildcard = $direct = 0;
        $container = (new ContainerBuilder(new Yaml))(
            new Path('container.yml'),
            (new Map('string', 'mixed'))->put(
                'listeners',
                (new Map('string', SetInterface::class))
                    ->put(
                        'Fixtures\Innmind\EventBus\*',
                        Set::of('callable', function() use (&$wildcard) {
                            ++$wildcard;
                        })
                    )
                    ->put(
                        'stdClass',
                        Set::of('callable', function() use (&$direct) {
                            ++$direct;
                        })
                    )
            )
        );

        $container->get('bus')->dispatch(new Foo);
        $this->assertSame(1, $wildcard);
        $this->assertSame(0, $direct);

        $container->get('bus')->dispatch(new \stdClass);
        $this->assertSame(1, $wildcard);
        $this->assertSame(1, $direct);
    }
}
