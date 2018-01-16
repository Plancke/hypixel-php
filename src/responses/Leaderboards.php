<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

/**
 * Class Leaderboards
 * @package Plancke\HypixelPHP\responses
 */
class Leaderboards extends HypixelObject {

    /**
     * @return string
     */
    function getCacheTimeKey() {
        return CacheTimes::LEADERBOARDS;
    }

}