<?php

namespace Plancke\HypixelPHP\responses\player;

use Plancke\HypixelPHP\classes\APIObject;
use Plancke\HypixelPHP\util\TimeUtils;

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
        return $this->getInt('coins');
    }

    /**
     * @param $stat
     * @return array|null|string|int
     */
    public function getWeeklyStat($stat) {
        return $this->get($stat . '_' . TimeUtils::getWeeklyOscillation());
    }

    /**
     * @param $stat
     * @return array|null|string|int
     */
    public function getMonthlyStat($stat) {
        return $this->get($stat . '_' . TimeUtils::getMonthlyOscillation());
    }
}