<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

/**
 * Class KeyInfo
 * @package Plancke\HypixelPHP\responses
 */
class KeyInfo extends HypixelObject {

    /**
     * @return int
     */
    public function getQueriesInPastMin() {
        return $this->getInt("queriesInPastMin");
    }

    /**
     * @return int
     */
    public function getTotalQueries() {
        return $this->getInt("totalQueries");
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->get('key');
    }

    /**
     * @return string
     */
    function getCacheTimeKey() {
        return CacheTimes::KEY_INFO;
    }

}