<?php

namespace Plancke\HypixelPHP\util;

abstract class CacheUtil {

    /**
     * Check whether or not a time has expired
     * All times supplied should be in milliseconds
     *
     * @param int $cachedTime timestamp of when something was cached
     * @param int $duration duration of cache
     * @param array|int $offset offset can be a number or array of numbers where a random will be picked,
     * positive offset to check whether or not it has been expired for X,
     * negative offset to check if it will expire in X
     *
     * @return bool whether or not it was expired
     */
    public static function isExpired($cachedTime, $duration, $offset = 0) {
        return self::getRemainingTime($cachedTime, $duration, $offset) > 0;
    }

    /**
     * Check how much time is left in the cache
     * All times supplied should be in milliseconds
     *
     * @param int $cachedTime timestamp of when something was cached
     * @param int $duration duration of cache
     * @param array|int $offset offset can be a number or array of numbers where a random will be picked
     *
     * @return int remaining time to keep cache
     */
    public static function getRemainingTime($cachedTime, $duration, $offset = 0) {
        $offsetValue = 0;
        if (is_array($offset)) {
            if (sizeof($offset) == 2) {
                $offsetValue = random_int($offset[0], $offset[1]);
            }
        } elseif (is_numeric($offset)) {
            $offsetValue = $offset;
        }

        return (time() * 1000) + $offsetValue - $duration - $cachedTime;
    }

    /**
     * Generate a filename for a given input, first few characters
     * become directories so less files per directory.
     * This improves speed for the OS
     *
     * @param $input
     * @param int $dirs
     *
     * @return string
     */
    public static function getCacheFileName($input, $dirs = 2) {
        $input = strtolower($input);
        $input = trim($input);
        $input = str_replace(' ', '%20', $input);

        if (strlen($input) <= $dirs) {
            $parts = str_split($input, 1);
        } else {
            $parts = [];
            for ($i = 0; $i < $dirs; $i++) {
                array_push($parts, substr($input, $i, 1));
            }
            array_push($parts, substr($input, $dirs));
        }

        return implode(DIRECTORY_SEPARATOR, $parts);
    }

}