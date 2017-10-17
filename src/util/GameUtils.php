<?php

namespace Plancke\HypixelPHP\util;

abstract class GameUtils {

    /**
     * Calculate level for given bedwars experience
     *
     * @param $exp
     * @return float|int
     */
    public static function getBedwarsLevel($exp) {
        // first few levels are different
        if ($exp < 500) {
            return 0;
        } else if ($exp < 1500) {
            return 1;
        } else if ($exp < 3500) {
            return 2;
        } else if ($exp < 5500) {
            return 3;
        } else if ($exp < 9000) {
            return 4;
        }

        $exp -= 9000;
        return $exp / 5000 + 4;
    }

}