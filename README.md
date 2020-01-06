# EventBus

| `develop` |
|-----------|
| [![codecov](https://codecov.io/gh/Innmind/EventBus/branch/develop/graph/badge.svg)](https://codecov.io/gh/Innmind/EventBus) |
| [![Build Status](https://github.com/Innmind/EventBus/workflows/CI/badge.svg)](https://github.com/Innmind/EventBus/actions?query=workflow%3ACI) |

Simple library to dispatch events to listeners; with the particularity that you can't order your listeners, listeners can't modify the event, listeners can't stop other listeners to be called and the event must be an object.

## Instalation

```sh
composer require innmind/event-bus
```

## Example

```php
use function Innmind\EventBus\bootstrap;
use Innmind\Immutable\Map;

class MyEvent{}

$echo = function(MyEvent $event): void {
    echo 'foo';
};

$dispatch = bootstrap()['bus'](
    Map::of('string', 'callable')
        (MyEvent::class, $echo)
);

$dispatch(new MyEvent); // will print "foo"
```

All listeners must be `callable`s and can listen to a specific class, a parent class or an interface.
