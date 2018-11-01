<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBus;

use Innmind\EventBus\Listeners;
use PHPUnit\Framework\TestCase;

class ListenersTest extends TestCase
{
    public function testInterface()
    {
        $called = [];
        $exception = new \Exception;
        $expected = new \stdClass;
        $listen = new Listeners(
            function($event) use (&$called, $expected) {
                $this->assertSame($expected, $event);
                $called[] = 'foo';
            },
            function($event) use (&$called, $expected) {
                $this->assertSame($expected, $event);
                $called[] = 'bar';
            },
            function($event) use ($expected, $exception) {
                $this->assertSame($expected, $event);

                throw $exception;
            }
        );

        try {
            $listen($expected);
            $this->fail('it should throw');
        } catch (\Throwable $e) {
            $this->assertSame($exception, $e);
        }
    }
}
