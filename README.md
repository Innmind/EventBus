# EventBus

| `master` | `develop` |
|----------|-----------|
| [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Innmind/EventBus/?branch=master) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/EventBus/?branch=develop) |
| [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Innmind/EventBus/?branch=master) | [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/EventBus/?branch=develop) |
| [![Build Status](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Innmind/EventBus/build-status/master) | [![Build Status](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/EventBus/build-status/develop) |

Simple library to dispatch events to listeners; with the particularity that you can't order your listeners, listeners can't modify the event, listeners can't stop other listeners to be called and the event must be an object.

## Instalation

```sh
composer require innmind/event-bus
```

## Example

```php
use function Innmind\EventBus\bootstrap;
use Innmind\Immutable\{
    Map,
    SetInterface,
    Set
};

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
