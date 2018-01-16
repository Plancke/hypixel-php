<?php

namespace Plancke\HypixelPHP\util;

/**
 * Class Validator
 * @package Plancke\HypixelPHP\util
 */
class Validator {

    /**
     * Matches UUIDs version 1 through 5
     */
    const UUID_ALL_VERSION_MATCHER = '~^[0-9a-f]{8}(-|)[0-9a-f]{4}(-|)[0-5][0-9a-f]{3}(-|)[89ab][0-9a-f]{3}(-|)[0-9a-f]{12}$~i';

    /**
     * @param $input
     * @return bool
     */
    public static function isValidAPIKey($input) {
        return self::isAnyUUID($input);
    }

    /**
     * @param $input
     * @return bool
     */
    public static function isAnyUUID($input) {
        return is_string($input) && (strlen($input) == 36 || strlen($input) == 32) // basic validation
            && preg_match(Validator::UUID_ALL_VERSION_MATCHER, $input); // actually check layout
    }

    /**
     * @param $input
     * @return bool
     */
    public static function isUsername($input) {
        return is_string($input) && strlen($input) <= 16 && strlen($input) > 0;
    }

}