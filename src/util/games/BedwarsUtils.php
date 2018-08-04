<?php

namespace Plancke\HypixelPHP\util\games;

/**
 * Class BedwarsUtils
 * @package Plancke\HypixelPHP\util
 */
class BedwarsUtils {

    const BEDWARS_EXP_PER_PRESTIGE = 489000;
    const BEDWARS_LEVELS_PER_PRESTIGE = 100;

    /**
     * Calculate level for given bedwars experience
     *
     * @param $exp
     * @return float|int
     */
    public function getBedwarsLevel($exp) {
        $prestige = (int)($exp / BedWarsUtils::BEDWARS_EXP_PER_PRESTIGE);
        $exp = $exp % BedWarsUtils::BEDWARS_EXP_PER_PRESTIGE;

        if ($prestige > 5) {
            $over = $prestige % 5;
            $exp += $over * BedWarsUtils::BEDWARS_EXP_PER_PRESTIGE;
            $prestige -= $over;
        }

        // first few levels are different
        if ($exp < 500) {
            return 0 + ($prestige * BedWarsUtils::BEDWARS_LEVELS_PER_PRESTIGE);
        } else if ($exp < 1500) {
            return 1 + ($prestige * BedWarsUtils::BEDWARS_LEVELS_PER_PRESTIGE);
        } else if ($exp < 3500) {
            return 2 + ($prestige * BedWarsUtils::BEDWARS_LEVELS_PER_PRESTIGE);
        } else if ($exp < 5500) {
            return 3 + ($prestige * BedWarsUtils::BEDWARS_LEVELS_PER_PRESTIGE);
        } else if ($exp < 9000) {
            return 4 + ($prestige * BedWarsUtils::BEDWARS_LEVELS_PER_PRESTIGE);
        }

        $exp -= 9000;
        return ($exp / 5000 + 4) + ($prestige * BedWarsUtils::BEDWARS_LEVELS_PER_PRESTIGE);
    }

}