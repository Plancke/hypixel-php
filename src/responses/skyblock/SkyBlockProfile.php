<?php

namespace Plancke\HypixelPHP\responses\skyblock;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

/**
 * Class SkyBlockProfile
 * @package Plancke\HypixelPHP\responses\skyblock
 */
class SkyBlockProfile extends HypixelObject {

    /**
     * @return string
     */
    public function getProfileId() {
        return $this->get('profile_id');
    }

    /**
     * @return array
     */
    public function getMembers() {
        return $this->get('members');
    }

    /**
     * @return string
     */
    public function getCacheTimeKey() {
        return CacheTimes::SKYBLOCK_PROFILE;
    }

    public function save() {
        $this->getHypixelPHP()->getCacheHandler()->setSkyBlockProfile($this);
    }
}