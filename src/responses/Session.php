<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\gameType\GameType;
use Plancke\HypixelPHP\classes\gameType\GameTypes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\fetch\FetchParams;

class Session extends HypixelObject {
    /**
     * @return array
     */
    public function getPlayers() {
        return $this->getArray('players');
    }

    /**
     * @return GameType|null
     */
    public function getGameType() {
        return GameTypes::fromEnum($this->get('gameType'));
    }

    /**
     * @return string
     */
    public function getServer() {
        return $this->get('server');
    }

    public function getPlayer() {
        $UUID = $this->getUUID();
        if ($UUID != null) {
            return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => $UUID]);
        }
        return null;
    }

    public function getUUID() {
        return $this->get('uuid');
    }

    function getCacheTimeKey() {
        return CacheTimes::SESSION;
    }
}