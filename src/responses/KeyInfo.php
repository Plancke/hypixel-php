<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

class KeyInfo extends HypixelObject {

    public function getQueriesInPastMin() {
        return $this->getInt("queriesInPastMin");
    }

    public function getTotalQueries() {
        return $this->getInt("totalQueries");
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->get('key');
    }

    function getCacheTimeKey() {
        return CacheTimes::KEY_INFO;
    }

}