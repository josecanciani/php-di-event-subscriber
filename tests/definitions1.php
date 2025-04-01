<?php

declare(strict_types = 1);

use Josecanciani\EventSubscriber\Tests\Events\OrderCreated;
use DI\Container;
use Josecanciani\EventSubscriber\Dispatcher;
use Josecanciani\EventSubscriber\Tests\Listeners\OrderCreatedListener1;
use function DI\decorate;

return [
    OrderCreated::class => decorate(function (OrderCreated $orderCreated, Container $container) {
        /** @var Dispatcher */
        $dispatcher = $container->get(Dispatcher::class);
        /** @var callable $listener */
        $listener = $container->get(OrderCreatedListener1::class);
        $dispatcher->addListener(OrderCreated::class, $listener);
        return $orderCreated;
    })
];
