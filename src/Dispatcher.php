<?php

declare(strict_types = 1);

namespace Josecanciani\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * A Symfony EventSubscriberInterface implementation that works with PHP-DI to add event listeners.
 * Since listeners are added using the decorate() pattern, calling the same event twice will add the same listeners twice.
 * So this class avoids adding them again. Apart from this, it's the same Symfony EventDispatcher class.
 */
class Dispatcher extends EventDispatcher {
    private $addedListeners = [];

    /**
     * @param callable|array $listener TODO: PHP8 Support
     */
    public function addListener(string $eventName, $listener, int $priority = 0): void {
        if ($this->addUniqueListener($eventName, $listener)) {
            parent::addListener($eventName, $listener, $priority);
        }
    }

    /**
     * @param callable|array $listener TODO: PHP8 Support
     */
    public function removeListener(string $eventName, $listener): void {
        throw new \LogicException('This object is not designed to remove listeners, since they are added by PHP-DI and not manually in code.');
    }

    /** We don't recommend using subscribers, as they usually add more listeners that what are actually used. Check project Readme.md. */
    public function addSubscriber(EventSubscriberInterface $subscriber): void {
        if ($this->addUniqueListener('__subscribers', $subscriber)) {
            parent::addSubscriber($subscriber);
        }
    }


    public function removeSubscriber(EventSubscriberInterface $subscriber): void {
        throw new \LogicException('This object is not designed to remove listeners, since they are added by PHP-DI and not manually in code.');
    }

     /**
     * @param callable|array $listener TODO: PHP8 Support
     */
    private function addUniqueListener(string $eventName, $listener): bool {
        if (!is_object($listener)) {
            throw new \LogicException('Only objects are supported, create one for each listener with an __invoke method');
        }
        if (!isset($this->addedListeners[$eventName])) {
            $this->addedListeners[$eventName] = [];
        }
        $id = spl_object_id($listener);
        if (in_array($id, $this->addedListeners[$eventName])) {
            return false;
        }
        $this->addedListeners[$eventName][] = $id;
        return true;
    }
}
