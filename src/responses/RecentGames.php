<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

/**
 * Class Status
 * @package Plancke\HypixelPHP\responses
 */
class RecentGames extends HypixelObject {

    public function getUUID() {
        return $this->get("uuid");
    }

    /**
     * @return array
     */
    public function getGames(): array {
        return $this->getArray('games');
    }

    /**
     * @return string
     */
    public function getCacheTimeKey(): string {
        return CacheTimes::RECENT_GAMES;
    }

    public function save() {
        $this->getHypixelPHP()->getCacheHandler()->setRecentGames($this);
    }
}