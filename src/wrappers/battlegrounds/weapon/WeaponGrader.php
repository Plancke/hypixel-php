<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds\weapon;

class WeaponGrader {

    /**
     * @param Weapon $weapon
     * @return float
     */
    public static function getGrade($weapon) {
        $rarityValues = RarityValues::get($weapon->getCategory());

        $percentages = [];

        foreach (WeaponStats::values() as $id) {
            $weaponStat = WeaponStats::fromID($id);
            $value = $rarityValues->getValue($weaponStat);
            if ($value == null) continue;
            if (!array_key_exists('min', $value)) continue;
            if (!array_key_exists('max', $value)) continue;

            array_push($percentages, $weapon->getBaseStat($weaponStat) / ($value['max'] - $value['min']));
        }

        return array_sum($percentages) / sizeof($percentages);
    }

}