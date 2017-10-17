<?php

namespace Plancke\HypixelPHP\responses\booster;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

class Boosters extends HypixelObject {
    /**
     * @param int $gameType
     * @param int $max
     *
     * @return array
     */
    public function getQueue($gameType, $max = 9999) {
        $return = [
            'boosters' => [],
            'total' => 0
        ];
        foreach ($this->getData() as $boosterInfo) {
            $booster = new Booster($this->getHypixelPHP(), $boosterInfo);
            if ($booster->getGameTypeID() == $gameType) {
                if ($return['total'] < $max) {
                    array_push($return['boosters'], $booster);
                }
                $return['total']++;
            }
        }
        return $return;
    }

    /**
     * @param $player
     * @return Booster[]
     */
    public function getBoosters($player) {
        $boosters = [];
        foreach ($this->getData() as $boosterInfo) {
            if (isset($boosterInfo['purchaserUuid']) && $boosterInfo['purchaserUuid'] == $player) {
                array_push($boosters, new Booster($this->getHypixelPHP(), $boosterInfo));
            }
        }
        return $boosters;
    }

    function getCacheTimeKey() {
        return CacheTimes::BOOSTERS;
    }

}