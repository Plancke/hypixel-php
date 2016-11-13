<?php

namespace Plancke\HypixelPHP\responses\friend;

use Plancke\HypixelPHP\classes\APIHolding;
use Plancke\HypixelPHP\fetch\FetchParams;

class Friend extends APIHolding {
    private $friend;
    private $uuid;

    public function __construct($HypixelPHP, $friend, $uuid) {
        parent::__construct($HypixelPHP);
        $this->friend = $friend;
        $this->uuid = $uuid;
    }

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

    public function getOtherPlayer() {
        if ($this->wasReceiver()) {
            return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => $this->friend['uuidSender']]);
        } else {
            return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => $this->friend['uuidReceiver']]);
        }
    }

    public function getSince() {
        return $this->friend['started'];
    }
}