<?php

namespace Plancke\HypixelPHP\resources;

use Plancke\HypixelPHP\responses\Resource;

/**
 * Class Resources
 * @package Plancke\HypixelPHP\resources
 */
abstract class Resources {

    const BASE_RESOURCES_DIR = __DIR__ . '/../../resources/';

    protected $resourceManager;

    /**
     * Resources constructor.
     * @param ResourceManager $resourceManager
     */
    public function __construct($resourceManager) {
        $this->resourceManager = $resourceManager;
    }

    /**
     * @param string $path
     * @return Resource
     */
    protected function requireResourceFile($path) {
        /** @noinspection PhpIncludeInspection */
        return new Resource($this->resourceManager->getHypixelPHP(), ['record' => require(self::BASE_RESOURCES_DIR . $path)], $path);
    }

    /**
     * @param $path
     * @return Resource
     */
    protected function requireRemoteResourceFile($path) {
        $return = $this->resourceManager->getHypixelPHP()->getResource($path);
        if ($return instanceof Resource) return $return;
        return null;
    }
}