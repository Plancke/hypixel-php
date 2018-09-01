<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds\weapon;

class WeaponStats {
    const DAMAGE = 0;
    const CHANCE = 1;
    const MULTIPLIER = 2;
    const ABILITY_BOOST = 3;
    const HEALTH = 4;
    const ENERGY = 5;
    const COOLDOWN = 6;
    const MOVEMENT = 7;

    /**
     * @param $ID
     * @return WeaponStat|null
     */
    public static function fromID($ID) {
        switch ($ID) {
            case WeaponStats::DAMAGE:
                return new WeaponStat('Damage', 'damage', 7.5, $ID);
            case WeaponStats::CHANCE:
                return new WeaponStat('Crit Chance', 'chance', 0, $ID);
            case WeaponStats::MULTIPLIER:
                return new WeaponStat('Crit Multiplier', 'multiplier', 0, $ID);
            case WeaponStats::ABILITY_BOOST:
                return new WeaponStat('Ability Boost', 'abilityBoost', 7.5, $ID);
            case WeaponStats::HEALTH:
                return new WeaponStat('Health', 'health', 25, $ID);
            case WeaponStats::ENERGY:
                return new WeaponStat('Energy', 'energy', 10, $ID);
            case WeaponStats::COOLDOWN:
                return new WeaponStat('Cooldown', 'cooldown', 7.5, $ID);
            case WeaponStats::MOVEMENT:
                return new WeaponStat('Movement', 'movement', 7.5, $ID);
        }
        return null;
    }

    /**
     * @return array
     */
    public static function values() {
        return [
            self::DAMAGE,
            self::CHANCE,
            self::MULTIPLIER,
            self::ABILITY_BOOST,
            self::HEALTH,
            self::ENERGY,
            self::COOLDOWN,
            self::MOVEMENT
        ];
    }
}