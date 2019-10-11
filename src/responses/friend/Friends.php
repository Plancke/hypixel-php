<?php

namespace Plancke\HypixelPHP\responses\friend;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\responses\player\Player;

/**
 * Class Friends
 * @package Plancke\HypixelPHP\responses\friend
 */
class Friends extends HypixelObject {
    protected $LIST;

    /**
     * @return Friend[]
     */
    public function getList() {
        if ($this->LIST == null) {
            $this->LIST = [];
            foreach ($this->getRawList() as $f) {
                array_push($this->LIST, new Friend($this->getHypixelPHP(), $f, $this->getUUID()));
            }
        }
        return $this->LIST;
    }

    /**
     * @return array
     */
    public function getRawList() {
        return $this->getArray('list');
    }

    /**
     * @return string
     */
    public function getUUID() {
        return $this->get("uuid");
    }

    /**
     * @return Player|null
     * @throws HypixelPHPException
     */
    public function getPlayer() {
        if (isset($this->data['uuid'])) {
            return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => $this->getUUID()]);
        }
        return null;
    }

    /**
     * @return string
     */
    public function getCacheTimeKey() {
        return CacheTimes::FRIENDS;
    }

    public function save() {
        $this->getHypixelPHP()->getCacheHandler()->setFriends($this);
    }

}