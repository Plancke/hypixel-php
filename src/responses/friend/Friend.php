<?php

namespace Plancke\HypixelPHP\responses\friend;

use Plancke\HypixelPHP\classes\APIHolding;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\fetch\Response;
use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\responses\player\Player;

/**
 * Class Friend
 * @package Plancke\HypixelPHP\responses\friend
 */
class Friend extends APIHolding {

    protected $friend;
    protected $uuid;

    public function __construct(HypixelPHP $HypixelPHP, $friend, $uuid) {
        parent::__construct($HypixelPHP);
        $this->friend = $friend;
        $this->uuid = $uuid;
    }

    /**
     * Returns whether or not the Player
     * sent the friend request
     *
     * @return bool
     */
    public function wasSender() {
        return !$this->wasReceiver();
    }

    /**
     * Returns whether or not the Player
     * received the friend request
     *
     * @return bool
     */
    public function wasReceiver() {
        return $this->friend['uuidReceiver'] == $this->uuid;
    }

    /**
     * @return null|Response|Player
     * @throws HypixelPHPException
     */
    public function getOtherPlayer() {
        if ($this->wasReceiver()) {
            return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => $this->friend['uuidSender']]);
        } else {
            return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => $this->friend['uuidReceiver']]);
        }
    }

    /**
     * @return int
     */
    public function getSince() {
        return $this->friend['started'];
    }
}