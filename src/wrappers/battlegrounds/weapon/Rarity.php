<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds\weapon;

class Rarity {
    const COMMON = "COMMON";
    const RARE = "RARE";
    const EPIC = "EPIC";
    const LEGENDARY = "LEGENDARY";

    /**
     * @param $ID
     * @return RarityValues|null
     */
    public static function getValues($ID) {
        return RarityValues::get($ID);
    }
}