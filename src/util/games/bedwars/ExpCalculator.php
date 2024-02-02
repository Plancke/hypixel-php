<?php

namespace Plancke\HypixelPHP\util\games\bedwars;

/**
 * Class ExpCalculator
 * @package Plancke\HypixelPHP\util\games\bedwars
 */
class ExpCalculator {

    const EASY_LEVELS = 4;
    const EASY_LEVELS_XP = [500, 1000, 2000, 3500];
    const EASY_LEVELS_XP_TOTAL = 7000;

    const XP_PER_LEVEL = 5000;
    const XP_PER_PRESTIGE = 96 * self::XP_PER_LEVEL + self::EASY_LEVELS_XP_TOTAL;

    const LEVELS_PER_PRESTIGE = 100;

    public function getPrestigeForExp($exp) {
        return self::getPrestigeForLevel(self::getLevelForExp($exp));
    }

    /**
     * @param $level
     * @return BedWarsPrestige|null
     */
    public function getPrestigeForLevel($level) {
        $prestige = floor($level / self::LEVELS_PER_PRESTIGE);
        return BedWarsPrestige::fromID(min($prestige, BedWarsPrestige::HIGHEST_PRESTIGE));
    }

    /**
     * Calculate level for given bedwars experience
     *
     * @param $exp
     * @return float
     */
    public function getLevelForExp($exp) {
        $prestiges = floor($exp / self::XP_PER_PRESTIGE);

        $level = $prestiges * self::LEVELS_PER_PRESTIGE;

        $expWithoutPrestiges = $exp - ($prestiges * self::XP_PER_PRESTIGE);
        for ($i = 1; $i <= self::EASY_LEVELS; ++$i) {
            $expForEasyLevel = self::getExpForLevelFromPrevLevelOfPres($i);
            if ($expWithoutPrestiges < $expForEasyLevel) {
                break;
            }
            $level++;
            $expWithoutPrestiges -= $expForEasyLevel;
        }
        $level += floor($expWithoutPrestiges / self::XP_PER_LEVEL);

        return $level;
    }

    public function getExpForLevelFromPrevLevelOfPres($level) {
        $respectedLevel = self::getLevelRespectingPrestige($level);
        if ($respectedLevel == 0) return 0;

        if ($respectedLevel <= self::EASY_LEVELS) {
            return self::EASY_LEVELS_XP[$respectedLevel - 1];
        }

        return self::XP_PER_LEVEL;
    }

    /**
     * Returns "2" instead of "102" if prestiges happen every 100 levels e.g.
     * @param $level
     * @return float|int
     */
    public function getLevelRespectingPrestige($level) {
        if ($level > BedWarsPrestige::HIGHEST_PRESTIGE * self::LEVELS_PER_PRESTIGE) {
            return $level - BedWarsPrestige::HIGHEST_PRESTIGE * self::LEVELS_PER_PRESTIGE;
        } else {
            return $level % self::LEVELS_PER_PRESTIGE;
        }
    }

}