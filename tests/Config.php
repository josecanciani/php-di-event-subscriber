<?php

declare(strict_types = 1);

namespace Josecanciani\EventSubscriber\Tests;

final class Config {
    /** @var Config|null */
    private static $instance;

    /**
     * @param array<string> $diDefinitionFiles
     */
    public static function create(
        string $tempDir,
        array $diDefinitionFiles
    ): void {
        if (self::$instance) {
            throw new \LogicException('Oops, Config was already created!');
        }
        $config = new Config();
        $config->tempDir = $tempDir;
        $config->diDefinitionFiles = $diDefinitionFiles;
        self::$instance = $config;
    }

    public static function get(): Config {
        if (!self::$instance) {
            throw new \LogicException('Oops, no Config here, be sure to bootstrap it using the create() method first');
        }
        return self::$instance;
    }

    /** @var string */
    private $tempDir;
    /** @var array<string> */
    private $diDefinitionFiles;

    /** A general temporary directory to use for things like proxies and compilation. */
    public function getTempDir(): string {
        return $this->tempDir;
    }

    /** @return array<string> Definition files for DI container */
    public function getDiDefinitionFiles(): array {
        return $this->diDefinitionFiles;
    }
}
