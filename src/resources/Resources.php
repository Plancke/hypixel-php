<?php

namespace Plancke\HypixelPHP\resources;

/**
 * Class Resources
 * @package Plancke\HypixelPHP\resources
 */
abstract class Resources {

    const BASE_RESOURCES_DIR = __DIR__ . '/../../resources/';

    /**
     * @param string $path
     * @return mixed
     */
    public static function includeResourceFile($path) {
        /** @noinspection PhpIncludeInspection */
        return include(self::BASE_RESOURCES_DIR . $path);
    }

    /**
     * @param string $path
     * @return mixed
     */
    public static function requireResourceFile($path) {
        /** @noinspection PhpIncludeInspection */
        return require(self::BASE_RESOURCES_DIR . $path);
    }
}