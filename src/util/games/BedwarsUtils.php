<?php

namespace Plancke\HypixelPHP\util\games;

/**
 * Class BedwarsUtils
 * @package Plancke\HypixelPHP\util
 */
class BedwarsUtils {

    const EASY_LEVELS = 4;
    const EASY_LEVELS_XP = 7000;
    const XP_PER_PRESTIGE = 96 * 5000 + BedwarsUtils::EASY_LEVELS_XP;
    const LEVELS_PER_PRESTIGE = 100;
    const HIGHEST_PRESTIGE = 10;

    public function getExpForLevel($level) {
        if ($level == 0) return 0;

        $respectedLevel = $this->getLevelRespectingPrestige($level);
        if ($respectedLevel > BedwarsUtils::EASY_LEVELS) {
            return 5000;
        }

        switch ($respectedLevel) {
            case 1:
                return 500;
            case 2:
                return 1000;
            case 3:
                return 2000;
            case 4:
                return 3500;
        }
        return 5000;
    }

    public function getLevelForExpRespectingPrestige($exp) {
        return $this->getLevelForExp($exp);
    }

    /**
     * Returns "2" instead of "102" if prestiges happen every 100 levels e.g.
     * @param $level
     * @return float|int
     */
    public function getLevelRespectingPrestige($level) {
        if ($level > BedwarsUtils::HIGHEST_PRESTIGE * BedwarsUtils::LEVELS_PER_PRESTIGE) {
            return $level - BedwarsUtils::HIGHEST_PRESTIGE * BedwarsUtils::LEVELS_PER_PRESTIGE;
        } else {
            return $level % BedwarsUtils::LEVELS_PER_PRESTIGE;
        }
    }

    /**
     * Calculate level for given bedwars experience
     *
     * @param $exp
     * @return float|int
     */
    public function getLevelForExp($exp) {
        $prestiges = $exp / BedwarsUtils::XP_PER_PRESTIGE;

        $level = $prestiges * BedwarsUtils::LEVELS_PER_PRESTIGE;

        $expWithoutPrestiges = $exp - ($prestiges * BedwarsUtils::XP_PER_PRESTIGE);
        for ($i = 1; $i <= BedwarsUtils::EASY_LEVELS; ++$i) {
            $expForEasyLevel = $this->getExpForLevel($i);
            if ($expWithoutPrestiges < $expForEasyLevel) {
                break;
            }
            $level++;
            $expWithoutPrestiges -= $expForEasyLevel;
        }
        $level += $expWithoutPrestiges / 5000;

        return $level;
    }

}