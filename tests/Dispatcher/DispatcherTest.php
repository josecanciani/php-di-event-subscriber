<?php

namespace Josecanciani\EventSubscriber\Tests\Dispatcher;

use Josecanciani\EventSubscriber\Dispatcher;
use Josecanciani\EventSubscriber\Tests\Config;
use Josecanciani\EventSubscriber\Tests\DiContainer;
use Josecanciani\EventSubscriber\Tests\Events\OrderCreated;
use Josecanciani\EventSubscriber\Tests\Listeners\OrderCreatedListener1;
use Josecanciani\EventSubscriber\Tests\Listeners\OrderCreatedListener2;
use Josecanciani\EventSubscriber\Tests\Observer;
use PHPUnit\Framework\TestCase;

class DispatcherTest extends TestCase {
    /** @var \DI\Container */
    private $container;

    public function setUp(): void {
        parent::setUp();
        $this->container = DiContainer::_reset(Config::get());
    }

    public function testDispatcherAndListenerInstantiation(): void {
        /** @var Observer $observer */
        $observer = $this->container->get(Observer::class);
        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->container->get(Dispatcher::class);
        $this->assertEquals(0, $observer->getInstanciationsCount(OrderCreated::class), 'No instantiations yet should exist');
        $this->assertEquals(0, count($dispatcher->getListeners()), 'No listeners yet should exist, they should be added when an event object is created');
        /** @var OrderCreated $event1 */
        $event1 = $this->container->make(OrderCreated::class);
        $this->assertEquals(1, $observer->getInstanciationsCount(OrderCreated::class), 'Even though we have two listeners, only one order was created');
        $this->assertEquals(2, count($dispatcher->getListeners(OrderCreated::class)), 'We added two listeners in the definition*.php files');
        /** @var OrderCreated $event2 */
        $event2 = $this->container->make(OrderCreated::class);
        $this->assertEquals(2, $observer->getInstanciationsCount(OrderCreated::class), 'We requested a new Event class, so now the count should increase');
        $this->assertEquals(2, count($dispatcher->getListeners(OrderCreated::class)), 'No new listeners should be added!');
        $this->assertNotEquals(spl_object_id($event1), spl_object_id($event2), 'The events should be different objects!');
    }

    public function testDispatchCall(): void {
        /** @var Observer $observer */
        $observer = $this->container->get(Observer::class);
        /** @var OrderCreated $event */
        $event = $this->container->make(OrderCreated::class);
        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->container->get(Dispatcher::class);
        $this->assertEquals(0, $observer->getinvocationCount(OrderCreatedListener1::class), 'No dispatch yet, should not have invocations');
        $this->assertEquals(0, $observer->getinvocationCount(OrderCreatedListener2::class), 'No dispatch yet, should not have invocations');
        $dispatcher->dispatch($event);
        $this->assertEquals(1, $observer->getinvocationCount(OrderCreatedListener1::class), 'Dispatch done, we expect one invocation.');
        $this->assertEquals(1, $observer->getinvocationCount(OrderCreatedListener2::class), 'Dispatch done, we expect one invocation.');
    }
}
