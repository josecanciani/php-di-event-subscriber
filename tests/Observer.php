<?php

declare(strict_types = 1);

namespace Josecanciani\EventSubscriber\Tests;

class Observer {
    /** @var array<string, int> */
    private $instanciations = [];
    /** @var array<string, int> */
    private $invocations = [];

    public function increaseInstantiationCount(string $className): void {
        if (!isset($this->instanciations[$className])) {
            $this->instanciations[$className] = 0;
        }
        $this->instanciations[$className]++;
    }

    public function getInstanciationsCount(string $className): int {
        return isset($this->instanciations[$className]) ? $this->instanciations[$className] : 0;
    }

    public function increaseinvocationCount(string $listenerClass): void {
        if (!isset($this->invocations[$listenerClass])) {
            $this->invocations[$listenerClass] = 0;
        }
        $this->invocations[$listenerClass]++;
    }

    public function getinvocationCount(string $listenerClass): int {
        return isset($this->invocations[$listenerClass]) ? $this->invocations[$listenerClass] : 0;
    }
}
