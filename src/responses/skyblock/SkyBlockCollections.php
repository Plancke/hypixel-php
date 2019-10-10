<?php

namespace Plancke\HypixelPHP\responses\skyblock;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

/**
 * Class SkyBlockCollections
 * @package Plancke\HypixelPHP\responses\skyblock
 */
class SkyBlockCollections extends HypixelObject {

    /**
     * @return string
     */
    function getCacheTimeKey() {
        return CacheTimes::RESOURCE;
    }

}