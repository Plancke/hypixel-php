<?php

namespace Plancke\HypixelPHP\util;

/**
 * Class Utilities
 * @package Plancke\HypixelPHP\util
 */
abstract class Utilities {

    /**
     * Converts Hexadecimal color code into RGB
     * @param $hex
     * @return array
     * @deprecated
     */
    public static function hex2rgb($hex) {
        // replace the pound symbol just in case
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return [$r, $g, $b];
    }

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
        return $return ? $return : $default;
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