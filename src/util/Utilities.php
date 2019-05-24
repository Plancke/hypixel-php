<?php

namespace Plancke\HypixelPHP\util;

/**
 * Class Utilities
 * @package Plancke\HypixelPHP\util
 */
abstract class Utilities {

    /**
     * Get a value recursively in an array
     *
     * @param array $array
     * @param string $key
     * @param mixed $default default value to return if not found in array
     * @param string $delimiter where to split the key and go a level deeper in the array
     * @return mixed
     */
    public static function getRecursiveValue($array, $key, $default = null, $delimiter = '.') {
        $return = $array;
        foreach (explode($delimiter, $key) as $split) {
            if (!isset($return[$split])) return $default;
            $return = $return[$split];
        }
        return $return != null ? $return : $default;
    }

    /**
     * @param $uuid
     * @return string
     */
    public static function ensureDashedUUID($uuid) {
        if (strpos($uuid, "-")) {
            if (strlen($uuid) == 32) return $uuid;
            $uuid = Utilities::ensureNoDashesUUID($uuid);
        }
        return substr($uuid, 0, 8) . "-" . substr($uuid, 8, 12) . substr($uuid, 12, 16) . "-" . substr($uuid, 16, 20) . "-" . substr($uuid, 20, 32);
    }

    /**
     * @param $uuid
     * @return string
     */
    public static function ensureNoDashesUUID($uuid) {
        return str_replace("-", "", $uuid);
    }

}