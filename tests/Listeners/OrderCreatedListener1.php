<?php

declare(strict_types = 1);

namespace Josecanciani\EventSubscriber\Tests\Listeners;

use Josecanciani\EventSubscriber\Tests\Observer;

class OrderCreatedListener1 {
    /** @var Observer */
    private $observer;

    public function __construct(Observer $observer) {
        $this->observer = $observer;
        $this->observer->increaseInstantiationCount(static::class);
    }

    /** @return void */
    public function __invoke() {
        $this->observer->increaseinvocationCount(static::class);
    }
}
