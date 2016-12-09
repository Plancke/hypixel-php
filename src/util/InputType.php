<?php

namespace Plancke\HypixelPHP\util;

use Plancke\HypixelPHP\responses\player\Player;

abstract class InputType {

    const UUID = 0;
    const USERNAME = 1;
    const PLAYER_OBJECT = 2;

    /**
     * Determine input type
     * @param $input
     *
     * @return int|null
     */
    public static function getType($input) {
        if ($input instanceof Player) {
            return InputType::PLAYER_OBJECT;
        }

        if (self::isUUID($input)) {
            return InputType::UUID;
        }

        if (self::isUsername($input)) {
            return InputType::USERNAME;
        }

        return null;
    }

    public static function isUsername($input) {
        // TODO might want to add a charmatcher here
        return is_string($input) && strlen($input) <= 16;
    }

    public static function isUUID($input) {
        // TODO might want to add some validation here
        return is_string($input) && (strlen($input) == 36 || strlen($input) == 32);
    }
}