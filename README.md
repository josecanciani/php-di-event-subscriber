# php-di-event-subscriber

An Event Subscriber based on Symphony whose listeners are added using PHP-DI.

## Why?

Currently you need to setup all listeners in your application bootstrap process. This has a couple of problems:
* Registering a lot of listeners that you may not use in the request.
* Forcing you to have a centralized place to register all listeners.

## Alternatives

Some implementations requires parsing the code in search of static methods with a certain pattern, and then caching that code.
This requires extra steps and is hard to maintain.

## This solution

We take advante of the `DI\decorate` method to intercept the creation of an `Event` and only then we attach the listeners. Benefits:
* Each module can have their own PHP-DI definition files, so we have better modularization.
* Listeners are created on a JIT basis, which means that they are not loaded until they are actually needed.
* No bootstrap required (of course, you need PHP-DI installed, which most projects already have).
* PHP-DI caching takes care of the optimization.

## Usage

### Creating an Event

We use Symphony EventDispatcher components as our base. So creating an event is just extending the `Event` class:

Let's say we have this Event:

```php
namespace MyApp\Order\Events;

use Symfony\Contracts\EventDispatcher\Event;

class OrderCreated extends Event {
    public $orderId;
}
```

### Dispatching the Event

We instantiate Events using PHP-DI's `make` method. This will produce a new instance of the Event class.
Then we fill the Event with some data and we call our `Dispatcher` to trigger/dispatch it:

```php
use Josecanciani\EventSubscriber\Dispatcher;
use MyApp\Order\Events\OrderCreated;

class OrderDao {
    public function __construct(Dispatcher $dispatcher, Container $container) {
        $this->dispatcher = $dispatcher;
        $this->container = $container;
    }

    public function save() {
        $event = $this->container->make(OrderCreated::class);
        $event->orderId = 1234567890;

        $dispatcher = $this->dispatcher->get(Dispatcher::class);
        $dispatcher->dispatch($event);
    }
}

```

### Listening / Subscribing an Event

Let's say we have a `Fullfillment` module that needs to know when an Order has been created. There's two steps required.

First, create the `callable` class that will be triggered upon event dispatch.

```php
namespace MyApp\Fullfillment\Listeners;

use MyApp\Order\Events\OrderCreated;

class MyListener {
    public function __invoke(OrderCreated $event): void {
        // you action trigger goes here
        echo "Order {$event->orderId} has been created, doing something else now!\n";
    }
}
```

And second, register the listener the the module's `definitions.php` file:

```php
use Josecanciani\EventSubscriber\Dispatcher;
use DI\Container;
use function DI\decorate;
use MyApp\Order\Events\OrderCreated;

return [
    OrderCreated::class => decorate(function (OrderCreated $orderCreated, Container $container) {
        $container->get(Dispatcher::class)->addListener(
            OrderCreated::class,
            $container->get(MyListener::class);
        );
        return $orderCreated;
    })
];
```

That's it! Whenever the `OrderCreated` event is instantiated (with the `make()` method above), this decorator will register the listeners in the dispatcher. Like magic!

## Limitations

Only `callable` objects are supported as listeners. This is because we need to avoid registering the same listeners multiple times (in case the code calls the `make()` method more than once for the same event type). The Symphony Event Dispatcher supports string and array callbacks too, but we need to get a unique identifier of the listener to avoid registering it twice. And a callable object is easy to get the `spl_object_id` identification.

This limitiation helps with modularization as you can encapsulate the listener logic into one place, and you IDE can easily follow class references to navigate them.

## Roadmap

* PHP 8 code: write now we test against 7.2, but I intend to create a branch for it and move to a more modern PHP 8 codebase.
* Implement more features for the Hook pattern
  * Collectors: special events to recollect certain configuration options
  * Searchers
  * Cachable results: cache data in JSON or even in PHP code for faster retrieval.

## Testing

Just run `composer test`, it will run three different things:
1. check for code supporting the specific PHP version
2. run PHPStan to do static code analysis
3. run the PHPUnit unit tests.
