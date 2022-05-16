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
    const XP_PER_PRESTIGE = 96 * ExpCalculator::XP_PER_LEVEL + ExpCalculator::EASY_LEVELS_XP_TOTAL;

    const LEVELS_PER_PRESTIGE = 100;

    public function getPrestigeForExp($exp) {
        return ExpCalculator::getPrestigeForLevel(ExpCalculator::getLevelForExp($exp));
    }

    /**
     * @param $level
     * @return BedWarsPrestige|null
     */
    public function getPrestigeForLevel($level) {
        $prestige = floor($level / ExpCalculator::LEVELS_PER_PRESTIGE);
        return BedWarsPrestige::fromID(min($prestige, BedWarsPrestige::HIGHEST_PRESTIGE));
    }

    /**
     * Calculate level for given bedwars experience
     *
     * @param $exp
     * @return float
     */
    public function getLevelForExp($exp) {
        $prestiges = floor($exp / ExpCalculator::XP_PER_PRESTIGE);

        $level = $prestiges * ExpCalculator::LEVELS_PER_PRESTIGE;

        $expWithoutPrestiges = $exp - ($prestiges * ExpCalculator::XP_PER_PRESTIGE);
        for ($i = 1; $i <= ExpCalculator::EASY_LEVELS; ++$i) {
            $expForEasyLevel = ExpCalculator::getExpForLevel($i);
            if ($expWithoutPrestiges < $expForEasyLevel) {
                break;
            }
            $level++;
            $expWithoutPrestiges -= $expForEasyLevel;
        }
        $level += floor($expWithoutPrestiges / ExpCalculator::XP_PER_LEVEL);

        return $level;
    }

    public function getExpForLevel($level) {
        if ($level == 0) return 0;

        $respectedLevel = ExpCalculator::getLevelRespectingPrestige($level);
        if ($respectedLevel <= ExpCalculator::EASY_LEVELS) {
            return self::EASY_LEVELS_XP[$respectedLevel - 1];
        }

        return ExpCalculator::XP_PER_LEVEL;
    }

    /**
     * Returns "2" instead of "102" if prestiges happen every 100 levels e.g.
     * @param $level
     * @return float|int
     */
    public function getLevelRespectingPrestige($level) {
        if ($level > BedWarsPrestige::HIGHEST_PRESTIGE * ExpCalculator::LEVELS_PER_PRESTIGE) {
            return $level - BedWarsPrestige::HIGHEST_PRESTIGE * ExpCalculator::LEVELS_PER_PRESTIGE;
        } else {
            return $level % ExpCalculator::LEVELS_PER_PRESTIGE;
        }
    }

}