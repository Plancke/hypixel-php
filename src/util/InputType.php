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

        if (InputType::isUUID($input)) {
            return InputType::UUID;
        }

        if (is_string($input) && strlen($input) <= 16) {
            return InputType::USERNAME;
        }

        return null;
    }

    public static function isUUID($input) {
        return is_string($input) && (strlen($input) == 32 || strlen($input) == 28);
    }
}