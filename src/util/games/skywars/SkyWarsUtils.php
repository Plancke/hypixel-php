<?php

namespace Plancke\HypixelPHP\util\games\skywars;

/**
 * Class BedwarsUtils
 * @package Plancke\HypixelPHP\util
 */
class SkyWarsUtils {

    protected static $EXP_CALCULATOR;

    /**
     * @return ExpCalculator
     */
    public function getExpCalculator() {
        if (SkyWarsUtils::$EXP_CALCULATOR == null) {
            SkyWarsUtils::$EXP_CALCULATOR = new ExpCalculator();
        }
        return SkyWarsUtils::$EXP_CALCULATOR;
    }

}