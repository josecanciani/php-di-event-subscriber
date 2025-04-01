<?php

declare(strict_types = 1);

namespace Josecanciani\EventSubscriber\Tests\Events;

use Josecanciani\EventSubscriber\Tests\Observer;
use Symfony\Contracts\EventDispatcher\Event;

class OrderCreated extends Event {
    /** @var Observer */
    private $observer;

    public function __construct(Observer $observer) {
        $this->observer = $observer;
        $this->observer->increaseInstantiationCount(static::class);
    }
}
