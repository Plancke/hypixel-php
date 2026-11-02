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
            // Discard removed stats from existing weapons
            if (!$weapon->hasField($weaponStat->getField())) continue;

            $percentage = ($weapon->getBaseStat($weaponStat) - $value['min']) / ($value['max'] - $value['min']);
            // Clamp to 0-1 because legacy weapons may break this grading
            $percentages[] = max(0, min(1, $percentage));
        }

        if (sizeof($percentages) == 0) return 0; // just in case
        return array_sum($percentages) / sizeof($percentages);
    }

    /**
     * Whether any stat falls outside the current ranges, meaning unconverted
     *
     * @param Weapon $weapon
     * @return bool
     */
    public static function isLegacy($weapon) {
        $rarityValues = RarityValues::get($weapon->getCategory());
        if ($rarityValues == null) return false;

        foreach (WeaponStats::values() as $id) {
            $weaponStat = WeaponStats::fromID($id);
            $value = $rarityValues->getValue($weaponStat);
            if ($value == null || !array_key_exists('min', $value) || !array_key_exists('max', $value)) continue;
            if (!$weapon->hasField($weaponStat->getField())) continue;

            $stat = $weapon->getBaseStat($weaponStat);
            if ($stat < $value['min'] || $stat > $value['max']) return true;
        }
        return false;
    }

}