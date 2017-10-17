<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

class PlayerCount extends HypixelObject {

    /**
     * @return int
     */
    public function getPlayerCount() {
        return $this->getInt('playerCount');
    }

    function getCacheTimeKey() {
        return CacheTimes::PLAYER_COUNT;
    }
}