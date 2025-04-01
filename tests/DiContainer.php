<?php

declare(strict_types = 1);

namespace Josecanciani\EventSubscriber\Tests;

use DI\Container;
use DI\ContainerBuilder;

/** This is a PHP-DI Container holder */
final class DiContainer {
    /** @var Container|null */
    private static $instance;

    /** To be used only from a bootstrap file */
    public static function create(Config $config): Container {
        if (self::$instance) {
            throw new \LogicException('Oops, DI Container was already created!');
        }
        $builder = new ContainerBuilder();
        $builder->enableCompilation($config->getTempDir());
        $builder->writeProxiesToFile(true, $config->getTempDir() . DIRECTORY_SEPARATOR . '/proxies');
        $builder->addDefinitions(...$config->getDiDefinitionFiles());
        $builder->useAutowiring(true);
        self::$instance = $builder->build();
        return self::$instance;
    }

    public static function get(): Container {
        if (!self::$instance) {
            throw new \LogicException('Oops, no Container here, be sure to bootstrap it using the create() method first');
        }
        return self::$instance;
    }

    /** To be used for testing purposes.  */
    public static function _reset(Config $config): Container {
        self::$instance = null;
        return self::create($config);
    }
}
