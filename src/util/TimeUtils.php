<?php

namespace Plancke\HypixelPHP\util;

use DateTime;

/**
 * Class TimeUtils
 * @package Plancke\HypixelPHP\util
 */
abstract class TimeUtils {
    /**
     * Returns {@code a} or {@code b} depending on the current week
     * @return string
     */
    public static function getWeeklyOscillation() {
        date_default_timezone_set("America/New_York");
        $epoch = 1417237200000;
        $milli = round(microtime(true) * 1000);

        $delta = abs($milli - $epoch);
        $osc = $delta / 604800000;

        return $osc % 2 == 0 ? "a" : "b";
    }

    /**
     * Returns {@code a} or {@code b} depending on the current month
     * @return string
     */
    public static function getMonthlyOscillation() {
        date_default_timezone_set("America/New_York");
        $epoch = 1417410000000;

        $dateStart = new DateTime(date("Y-m-d"));
        $dateEnd = new DateTime(date("Y-m-d", $epoch / 1000));

        $diffYear = $dateEnd->format("Y") - $dateStart->format("Y");
        /* @var $diffYear int */
        $diffMonth = $diffYear * 12 + TimeUtils::getJavaMonth($dateEnd) - TimeUtils::getJavaMonth($dateStart);

        return $diffMonth % 2 == 0 ? "a" : "b";
    }

    /**
     * @param DateTime $date
     * @return int
     */
    public static function getJavaMonth(DateTime $date) {
        date_default_timezone_set("America/New_York");
        return $date->format("n") - 1;
    }
}