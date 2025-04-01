<?php

declare(strict_types = 1);

namespace Josecanciani\EventSubscriber\Tests;

(function () {
    $root = __DIR__;
    Config::create(
        $root . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tmp',
        [
            $root . DIRECTORY_SEPARATOR . 'definitions1.php',
            $root . DIRECTORY_SEPARATOR . 'definitions2.php'
        ]
    );
    DiContainer::create(Config::get());
})();
