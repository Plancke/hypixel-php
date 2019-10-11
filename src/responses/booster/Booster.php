<?php

namespace Plancke\HypixelPHP\responses\booster;

use Plancke\HypixelPHP\classes\APIObject;
use Plancke\HypixelPHP\classes\gameType\GameType;
use Plancke\HypixelPHP\classes\gameType\GameTypes;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\fetch\Response;
use Plancke\HypixelPHP\responses\player\Player;

/**
 * Class Booster
 * @package Plancke\HypixelPHP\responses\booster
 */
class Booster extends APIObject {

    /**
     * @return Player|Response|null
     * @throws HypixelPHPException
     */
    public function getOwner() {
        return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => $this->getOwnerUUID()]);
    }

    public function getOwnerUUID() {
        return $this->get('purchaserUuid');
    }

    /**
     * @return GameType|null
     */
    public function getGameType() {
        return GameTypes::fromID($this->getGameTypeID());
    }

    /**
     * @return int
     */
    public function getGameTypeID() {
        return $this->get('gameType');
    }

    /**
     * @return bool
     */
    public function isActive() {
        // make sure it has ticked once at least
        return $this->getLength() != $this->getOriginalLength();
    }

    /**
     * @return int
     * @internal param bool $original
     */
    public function getLength() {
        return $this->getNumber('length');
    }

    /**
     * @return int
     */
    public function getOriginalLength() {
        // default to 1 hour
        return $this->getNumber('originalLength', 3600);
    }

    /**
     * @return int
     */
    public function getActivateTime() {
        return $this->getNumber('dateActivated');
    }

    /**
     * @return array
     */
    public function getStacked() {
        return $this->getArray('stacked');
    }

    /**
     * @return double
     */
    public function getAmount() {
        return $this->getDouble('amount');
    }
}