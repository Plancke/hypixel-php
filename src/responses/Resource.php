<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\HypixelPHP;

/**
 * Class Resource
 * @package Plancke\HypixelPHP\responses
 */
class Resource extends HypixelObject {
    protected $resource;

    public function __construct(HypixelPHP $HypixelPHP, $data, string $resource) {
        parent::__construct($HypixelPHP, $data);

        $this->resource = $resource;
    }

    /**
     * @return string
     */
    public function getResource() {
        return $this->resource;
    }

    public function save() {
        $this->getHypixelPHP()->getCacheHandler()->setResource($this);
    }

    /**
     * @return string
     */
    public function getCacheTimeKey() {
        return CacheTimes::RESOURCES;
    }
}