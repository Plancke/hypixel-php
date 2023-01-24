<?php

namespace Plancke\HypixelPHP\responses\counts;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\APIObject;

/**
 * Class GameCount
 * @package Plancke\HypixelPHP\responses\counts
 */
class GameCount extends APIObject {

    protected $data;

    /**
     * @return int
     */
    public function getPlayers() {
        return $this->getNumber('players');
    }

    /**
     * @return array
     */
    public function getModes() {
        return $this->getArray('modes');
    }

    /**
     * @return string
     */
    public function getCacheTimeKey() {
        return CacheTimes::COUNTS;
    }

}