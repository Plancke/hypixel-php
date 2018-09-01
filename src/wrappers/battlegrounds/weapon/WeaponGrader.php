<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds\weapon;

class WeaponGrader {

    /**
     * @param Weapon $weapon
     * @return float
     */
    public static function getGrade($weapon) {
        $rarityValues = RarityValues::get($weapon->getCategory());
        if ($rarityValues == null) return 0;

        $percentages = [];

        foreach (WeaponStats::values() as $id) {
            $weaponStat = WeaponStats::fromID($id);
            $value = $rarityValues->getValue($weaponStat);
            if ($value == null) continue;
            if (!array_key_exists('min', $value)) continue;
            if (!array_key_exists('max', $value)) continue;

            array_push($percentages, ($weapon->getBaseStat($weaponStat) - $value['min']) / ($value['max'] - $value['min']));
        }

        if (sizeof($percentages) == 0) return 0; // just in case
        return array_sum($percentages) / sizeof($percentages);
    }

}