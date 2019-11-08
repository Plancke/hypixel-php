<?php

namespace Plancke\HypixelPHP\util\games\bedwars;

/**
 * Class BedWarsUtils
 * @package Plancke\HypixelPHP\util\games\bedwars
 */
class BedWarsUtils {

    protected static $EXP_CALCULATOR;

    /**
     * @return ExpCalculator
     */
    public function getExpCalculator() {
        if (BedWarsUtils::$EXP_CALCULATOR == null) {
            BedWarsUtils::$EXP_CALCULATOR = new ExpCalculator();
        }
        return BedWarsUtils::$EXP_CALCULATOR;
    }

}