<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\gameType\GameType;
use Plancke\HypixelPHP\classes\gameType\GameTypes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\fetch\Response;
use Plancke\HypixelPHP\responses\player\Player;

/**
 * Class Session
 * @package Plancke\HypixelPHP\responses
 */
class Session extends HypixelObject {
    /**
     * @return array
     */
    public function getPlayers() {
        return $this->getArray('players');
    }

    /**
     * @return GameType
     */
    public function getGameType() {
        $val = $this->get('gameType');
        if ($val == null) {
            return null;
        }
        return GameTypes::fromEnum($val);
    }

    /**
     * @return string
     */
    public function getServer() {
        return $this->get('server');
    }

    /**
     * @return null|Response|Player
     * @throws HypixelPHPException
     */
    public function getPlayer() {
        $UUID = $this->getUUID();
        if ($UUID != null) {
            return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => $UUID]);
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getUUID() {
        return $this->get('uuid');
    }

    /**
     * @return string
     */
    public function getCacheTimeKey() {
        return CacheTimes::SESSION;
    }

    public function save() {
        $this->getHypixelPHP()->getCacheHandler()->setSession($this);
    }
}