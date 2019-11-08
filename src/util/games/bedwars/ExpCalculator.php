<?php

namespace Plancke\HypixelPHP\util\games\bedwars;

/**
 * Class ExpCalculator
 * @package Plancke\HypixelPHP\util\games\bedwars
 */
class ExpCalculator {

    const EASY_LEVELS = 4;
    const EASY_LEVELS_XP = 7000;
    const XP_PER_PRESTIGE = 96 * 5000 + ExpCalculator::EASY_LEVELS_XP;

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

        switch ($prestige) {
            case 0:
                return BedWarsPrestige::fromID(BedWarsPrestige::NONE);
            case 1:
                return BedWarsPrestige::fromID(BedWarsPrestige::IRON);
            case 2:
                return BedWarsPrestige::fromID(BedWarsPrestige::GOLD);
            case 3:
                return BedWarsPrestige::fromID(BedWarsPrestige::DIAMOND);
            case 4:
                return BedWarsPrestige::fromID(BedWarsPrestige::EMERALD);
            case 5:
                return BedWarsPrestige::fromID(BedWarsPrestige::SAPPHIRE);
            case 6:
                return BedWarsPrestige::fromID(BedWarsPrestige::RUBY);
            case 7:
                return BedWarsPrestige::fromID(BedWarsPrestige::CRYSTAL);
            case 8:
                return BedWarsPrestige::fromID(BedWarsPrestige::OPAL);
            case 9:
                return BedWarsPrestige::fromID(BedWarsPrestige::AMETHYST);
            case 10:
            default:
                return BedWarsPrestige::fromID(BedWarsPrestige::RAINBOW);
        }
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
        $level += floor($expWithoutPrestiges / 5000);

        return $level;
    }

    public function getExpForLevel($level) {
        if ($level == 0) return 0;

        $respectedLevel = ExpCalculator::getLevelRespectingPrestige($level);
        if ($respectedLevel > ExpCalculator::EASY_LEVELS) {
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