<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds;

class PlayerClasses {
    const MAGE = 0;
    const WARRIOR = 1;
    const PALADIN = 2;
    const SHAMAN = 3;

    /**
     * @param int $ID
     * @param int $SPEC
     * @return PlayerClass|null
     */
    public static function fromID($ID, $SPEC) {
        switch ($ID) {
            case PlayerClasses::MAGE:
                return new PlayerClass("mage", $SPEC, $ID);
            case PlayerClasses::WARRIOR:
                return new PlayerClass("warrior", $SPEC, $ID);
            case PlayerClasses::PALADIN:
                return new PlayerClass("paladin", $SPEC, $ID);
            case PlayerClasses::SHAMAN:
                return new PlayerClass("shaman", $SPEC, $ID);
        }
        return null;
    }
}