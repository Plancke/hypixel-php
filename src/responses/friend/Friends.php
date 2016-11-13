<?php

namespace Plancke\HypixelPHP\responses\friend;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\fetch\FetchParams;

class Friends extends HypixelObject {
    private $LIST;

    /**
     * @return Friend[]
     */
    public function getList() {
        if ($this->LIST == null) {
            $this->LIST = [];
            foreach ($this->getRawList() as $f) {
                array_push($this->LIST, new Friend($f, $this->getHypixelPHP(), $this->getUUID()));
            }
        }
        return $this->LIST;
    }

    public function getRawList() {
        return $this->get('list', true, []);
    }

    public function getUUID() {
        return $this->get("uuid");
    }

    /**
     * @return \HypixelPHP\responses\player\Player|null
     */
    public function getPlayer() {
        if (isset($this->data['uuid'])) {
            return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => $this->getUUID()]);
        }
        return null;
    }

    function getCacheTimeKey() {
        return CacheTimes::FRIENDS;
    }

}