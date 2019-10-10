<?php

namespace Plancke\HypixelPHP\responses\skyblock;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

/**
 * Class SkyBlockNews
 * @package Plancke\HypixelPHP\responses\skyblock
 */
class SkyBlockNews extends HypixelObject {

    /**
     * @return array
     */
    public function getItems() {
        return $this->getArray("items");
    }

    /**
     * @return string
     */
    function getCacheTimeKey() {
        return CacheTimes::RESOURCE;
    }

}