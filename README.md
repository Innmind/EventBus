# EventBus

| `master` | `develop` |
|----------|-----------|
| [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Innmind/EventBus/?branch=master) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/EventBus/?branch=develop) |
| [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Innmind/EventBus/?branch=master) | [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/EventBus/?branch=develop) |
| [![Build Status](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Innmind/EventBus/build-status/master) | [![Build Status](https://scrutinizer-ci.com/g/Innmind/EventBus/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/EventBus/build-status/develop) |

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2ebb7dad-6e0b-41d8-9808-762c128a6e74/big.png)](https://insight.sensiolabs.com/projects/2ebb7dad-6e0b-41d8-9808-762c128a6e74)

Simple library to dispatch events to listeners; with the particularity that you can't order your listeners, listeners can't modify the event, listeners can't stop other listeners to be called and the event must be an object.

Example of a simple case:
```php
use Innmind\{
    EventBus\EventBus,
    Immutable\Map,
    Immutable\SetInterface,
    Immutable\Set
};

class MyEvent{}

$dispatcher = new EventBus(
    (new Map('string', SetInterface::class))
        ->put(
            MyEvent::class,
            (new Set('callable'))
                ->add(function (MyEvent $event) {
                    echo 'foo';
                })
        )
);

$dispatcher->dispatch(new MyEvent); // will print "foo"
```

All listeners must be `callable`s and can listen to a specific class, a parent class or an interface.
