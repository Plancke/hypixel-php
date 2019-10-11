<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

/**
 * Class WatchdogStats
 * @package Plancke\HypixelPHP\responses
 */
class WatchdogStats extends HypixelObject {
    /**
     * @return int
     */
    public function getLastMinute() {
        return $this->getNumber('watchdog_lastMinute');
    }

    /**
     * @return int
     */
    public function getTotal() {
        return $this->getNumber('watchdog_lastMinute');
    }

    /**
     * @return int
     */
    public function getRollingDaily() {
        return $this->getNumber('watchdog_rollingDaily');
    }

    /**
     * @return string
     */
    public function getCacheTimeKey() {
        return CacheTimes::WATCHDOG;
    }

    public function save() {
        $this->getHypixelPHP()->getCacheHandler()->setWatchdogStats($this);
    }
}