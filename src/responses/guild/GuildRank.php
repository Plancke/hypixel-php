<?php

namespace Plancke\HypixelPHP\responses\guild;

use Plancke\HypixelPHP\classes\APIObject;
use Plancke\HypixelPHP\HypixelPHP;

class GuildRank extends APIObject {

    /**
     * @param HypixelPHP $HypixelPHP
     * @param $rank
     */
    public function __construct(HypixelPHP $HypixelPHP, $rank) {
        parent::__construct($HypixelPHP, $rank);
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->get("name");
    }

    /**
     * @return array
     */
    public function getPermissions() {
        return $this->getArray("permissions");
    }

    /**
     * @return bool
     */
    public function isDefault() {
        return $this->get('default', false);
    }

    /**
     * @return string
     */
    public function getTag() {
        return $this->get("tag");
    }

    /**
     * @return int
     */
    public function getCreated() {
        return $this->getInt("created");
    }

    /**
     * @return int
     */
    public function getPriority() {
        return $this->getInt('priority');
    }
}