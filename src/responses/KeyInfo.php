<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

class KeyInfo extends HypixelObject {

    public function handleNew() {
        $extraSetter = [];

        $HISTORY = $this->getExtra("requestHistory");
        if (!is_array($HISTORY)) {
            $HISTORY = [];
        }
        $HISTORY[time()] = $this->getInt("totalQueries", 0);
        //$extraSetter["requestHistory"] = $HISTORY;

        $HIGHEST = $this->getExtra("highestRequests");
        $LAST_MIN = $this->getQueriesInPastMin();
        if ($LAST_MIN > $HIGHEST) {
            $extraSetter["highestRequests"] = $LAST_MIN;
        }

        $this->setExtra($extraSetter);
    }

    public function getQueriesInPastMin() {
        return $this->getInt("queriesInPastMin");
    }

    public function getTotalQueries() {
        return $this->getInt("totalQueries");
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->get('key');
    }

    function getCacheTimeKey() {
        return CacheTimes::KEY_INFO;
    }

}