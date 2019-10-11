<?php

namespace Plancke\HypixelPHP\responses\player;

use Plancke\HypixelPHP\classes\APIObject;

/**
 * Class GameStats
 * @package Plancke\HypixelPHP\responses\player
 */
class GameStats extends APIObject {

    /**
     * @return array|null
     */
    public function getPackages() {
        return $this->getArray('packages');
    }

    /**
     * @param $package
     * @return bool
     */
    public function hasPackage($package) {
        return in_array($package, $this->getArray('packages'));
    }

    /**
     * @return int
     */
    public function getCoins() {
        return $this->getNumber('coins');
    }

}