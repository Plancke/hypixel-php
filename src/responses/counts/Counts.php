<?php

namespace Plancke\HypixelPHP\responses\counts;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\classes\serverType\ServerTypes;

/**
 * Class Counts
 * @package Plancke\HypixelPHP\responses\counts
 */
class Counts extends HypixelObject {

    protected $games = [];
    protected $playerCount = 0;

    /**
     * @param $gameTypeId
     * @return GameCount
     */
    public function getGameCounts($gameTypeId) {
        if (!array_key_exists($gameTypeId, $this->games)) {
            $this->games[$gameTypeId] = new GameCount($this->getHypixelPHP(), $this->getArray(ServerTypes::fromID($gameTypeId)->getEnum()));
        }
        return $this->games[$gameTypeId];
    }

    /**
     * @return int
     */
    public function getPlayerCount(): int {
        return $this->playerCount;
    }

    /**
     * @return string
     */
    public function getCacheTimeKey() {
        return CacheTimes::COUNTS;
    }

    public function save() {
        $this->getHypixelPHP()->getCacheHandler()->setCounts($this);
    }
}