<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

/**
 * Class PlayerCount
 * @package Plancke\HypixelPHP\responses
 */
class PlayerCount extends HypixelObject {

    /**
     * @return int
     */
    public function getPlayerCount() {
        return $this->getInt('playerCount');
    }

    /**
     * @return string
     */
    function getCacheTimeKey() {
        return CacheTimes::PLAYER_COUNT;
    }
}