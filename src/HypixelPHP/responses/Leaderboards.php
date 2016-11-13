<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

class Leaderboards extends HypixelObject {

    function getCacheTimeKey() {
        return CacheTimes::LEADERBOARDS;
    }

}