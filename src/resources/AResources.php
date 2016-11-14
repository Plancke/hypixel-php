<?php

namespace Plancke\HypixelPHP\resources;

abstract class AResources {

    const BASE_RESOURCES_DIR = __DIR__ . '/../../resources/';

    public static function includeResourceFile($path) {
        /** @noinspection PhpIncludeInspection */
        return include(self::BASE_RESOURCES_DIR . $path);
    }

    public static function requireResourceFile($path) {
        /** @noinspection PhpIncludeInspection */
        return require(self::BASE_RESOURCES_DIR . $path);
    }
}