<?php

namespace Plancke\HypixelPHP\responses\gameCounts;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\gameType\GameTypes;
use Plancke\HypixelPHP\classes\HypixelObject;

/**
 * Class GameCounts
 * @package Plancke\HypixelPHP\responses\gameCounts
 */
class GameCounts extends HypixelObject {

    protected $counts = [];

    /**
     * @param $gameTypeId
     * @return GameCount
     */
    public function getCounts($gameTypeId) {
        if (!array_key_exists($gameTypeId, $this->counts)) {
            $this->counts[$gameTypeId] = new GameCount($this->getHypixelPHP(), $this->getArray(GameTypes::fromID($gameTypeId)->getEnum()));
        }
        return $this->counts[$gameTypeId];
    }

    /**
     * @return string
     */
    public function getCacheTimeKey() {
        return CacheTimes::GAME_COUNTS;
    }

    public function save() {
        $this->getHypixelPHP()->getCacheHandler()->setGameCounts($this);
    }
}