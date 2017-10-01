<?php

namespace Plancke\HypixelPHP\responses\guild;

use DateTime;
use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\util\Utilities;

class Guild extends HypixelObject {

    public function handleNew($cached) {
        parent::handleNew($cached);

        $extraSetter = [];
        $extraSetter['coinHistory'] = $this->handleCoinHistory();

        // for mongo
        $extraSetter['name_lower'] = strtolower($this->getName());

        $this->setExtra($extraSetter, false);
    }

    protected function handleCoinHistory() {
        $coinHistory = $this->getExtra('coinHistory', []);
        foreach ($this->getData() as $key => $val) {
            if (strpos($key, 'dailyCoins') !== false) {
                $EXPLOSION = explode('-', $key);
                $coinHistory[$EXPLOSION[1] . '-' . ($EXPLOSION[2] + 1) . '-' . $EXPLOSION[3]] = $val;
            }
        }
        return $coinHistory;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->get('name');
    }

    /**
     * @return MemberList
     */
    public function getMemberList() {
        return new MemberList($this->getHypixelPHP(), $this->getArray('members'));
    }

    /**
     * @return bool
     */
    public function canTag() {
        return $this->get('canTag', false);
    }

    /**
     * @return string
     */
    public function getTag() {
        return Utilities::parseColors($this->get('tag'));
    }

    /**
     * @return int
     */
    public function getCoins() {
        return $this->getInt('coins');
    }

    /**
     * @return int
     */
    public function getMaxMembers() {
        $total = 25;
        $level = $this->getInt('memberSizeLevel', -1);
        if ($level >= 0) {
            $total += 5 * $level;
        }
        return $total;
    }

    /**
     * @return int
     */
    public function getMemberCount() {
        return $this->getMemberList()->getMemberCount();
    }

    /**
     * get coin history of Guild
     * @return array
     */
    public function getGuildCoinHistory() {
        if (!array_key_exists('coinHistory', $this->getExtra())) {
            $this->handleCoinHistory();
        }
        $coinHistory = $this->getExtra('coinHistory', []);

        $sortHistory = [];
        foreach ($coinHistory as $DATE => $AMOUNT) {
            array_push($sortHistory, [$DATE, $AMOUNT]);
        }

        usort($sortHistory, function ($a, $b) {
            $ad = new DateTime($a[0]);
            $bd = new DateTime($b[0]);

            if ($ad == $bd) {
                return 0;
            }

            return $ad < $bd ? 0 : 1;
        });

        return $sortHistory;
    }

    function getCacheTimeKey() {
        return CacheTimes::GUILD;
    }

}