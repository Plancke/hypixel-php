<?php

namespace Plancke\HypixelPHP\util;

use DateTime;
use DateTimeZone;
use Exception;

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
        $epoch = 1417237200000;
        $milli = round(microtime(true) * 1000);

        $delta = abs($milli - $epoch);
        $osc = $delta / 604800000;

        return $osc % 2 == 0 ? "a" : "b";
    }

    /**
     * Returns {@code a} or {@code b} depending on the current month
     * @return string
     * @throws Exception
     */
    public static function getMonthlyOscillation() {
        $epoch = 1417410000000;

        $dateStart = new DateTime(date("Y-m-d"), self::getHypixelTimeZone());
        $dateEnd = new DateTime(date("Y-m-d", $epoch / 1000), self::getHypixelTimeZone());

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
        return $date->format("n") - 1;
    }

    public static function getHypixelTimeZone() {
        return new DateTimeZone("America/New_York");
    }
}