<?php

namespace Plancke\HypixelPHP\util\games\skywars;

/**
 * Class SkyWarsUtils
 * @package Plancke\HypixelPHP\util\games\skywars
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