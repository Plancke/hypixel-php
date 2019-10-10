<?php

namespace Plancke\HypixelPHP\responses\skyblock;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

/**
 * Class SkyBlockSkills
 * @package Plancke\HypixelPHP\responses\skyblock
 */
class SkyBlockSkills extends HypixelObject {

    /**
     * @return string
     */
    function getCacheTimeKey() {
        return CacheTimes::RESOURCE;
    }

}