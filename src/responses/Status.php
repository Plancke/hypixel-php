<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\serverType\ServerType;
use Plancke\HypixelPHP\classes\serverType\ServerTypes;
use Plancke\HypixelPHP\classes\HypixelObject;

/**
 * Class Status
 * @package Plancke\HypixelPHP\responses
 */
class Status extends HypixelObject {

    public function getUUID() {
        return $this->get("uuid");
    }

    /**
     * @return bool
     */
    public function isOnline() {
        return $this->get('session.online', false);
    }

    /**
     * @return ServerType
     */
    public function getGameType() {
        $val = $this->get('session.gameType');
        if ($val == null) return null;
        return ServerTypes::fromEnum($val);
    }

    /**
     * @return string
     */
    public function getMode() {
        return $this->get('session.mode');
    }

    /**
     * @return string
     */
    public function getCacheTimeKey() {
        return CacheTimes::STATUS;
    }

    public function save() {
        $this->getHypixelPHP()->getCacheHandler()->setStatus($this);
    }
}