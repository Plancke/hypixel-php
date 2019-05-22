<?php

namespace Plancke\HypixelPHP\util;

use Plancke\HypixelPHP\responses\player\Player;

/**
 * Class InputType
 * @package Plancke\HypixelPHP\util
 */
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
        if ($input instanceof Player) return InputType::PLAYER_OBJECT;

        if (Validator::isAnyUUID($input)) return InputType::UUID;

        if (Validator::isUsername($input)) return InputType::USERNAME;

        return null;
    }
}