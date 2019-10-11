<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds\weapon;

class RarityValues {

    /**
     * @var array[int]RarityValues
     */
    protected static $cache = null;

    /**
     * @var array[int]array[string]int
     */
    protected $values = [];

    /**
     * @param $ID
     * @return RarityValues|null
     */
    public static function get($ID) {
        RarityValues::init();
        if (array_key_exists($ID, RarityValues::$cache)) {
            return RarityValues::$cache[$ID];
        }
        return null;
    }

    public static function init() {
        if (RarityValues::$cache != null) return;
        RarityValues::$cache = [];

        RarityValues::$cache[Rarity::COMMON] = (new RarityValues())
            ->addValue(WeaponStats::DAMAGE, 90, 100)
            ->addValue(WeaponStats::CHANCE, 10, 18)
            ->addValue(WeaponStats::MULTIPLIER, 150, 170)
            ->addValue(WeaponStats::ABILITY_BOOST, 4, 6)
            ->addValue(WeaponStats::HEALTH, 180, 220);

        RarityValues::$cache[Rarity::RARE] = (new RarityValues())
            ->addValue(WeaponStats::DAMAGE, 95, 105)
            ->addValue(WeaponStats::CHANCE, 12, 20)
            ->addValue(WeaponStats::MULTIPLIER, 160, 180)
            ->addValue(WeaponStats::ABILITY_BOOST, 6, 8)
            ->addValue(WeaponStats::HEALTH, 200, 250)
            ->addValue(WeaponStats::ENERGY, 10, 18);

        RarityValues::$cache[Rarity::EPIC] = (new RarityValues())
            ->addValue(WeaponStats::DAMAGE, 100, 110)
            ->addValue(WeaponStats::CHANCE, 15, 20)
            ->addValue(WeaponStats::MULTIPLIER, 160, 190)
            ->addValue(WeaponStats::ABILITY_BOOST, 7, 9)
            ->addValue(WeaponStats::HEALTH, 220, 275)
            ->addValue(WeaponStats::ENERGY, 15, 20)
            ->addValue(WeaponStats::COOLDOWN, 3, 5);

        RarityValues::$cache[Rarity::LEGENDARY] = (new RarityValues())
            ->addValue(WeaponStats::DAMAGE, 110, 120)
            ->addValue(WeaponStats::CHANCE, 15, 25)
            ->addValue(WeaponStats::MULTIPLIER, 180, 200)
            ->addValue(WeaponStats::ABILITY_BOOST, 10, 15)
            ->addValue(WeaponStats::HEALTH, 250, 400)
            ->addValue(WeaponStats::ENERGY, 20, 25)
            ->addValue(WeaponStats::COOLDOWN, 5, 10)
            ->addValue(WeaponStats::MOVEMENT, 5, 10);
    }

    /**
     * @param $id
     * @param $min
     * @param $max
     * @return $this
     */
    public function addValue($id, $min, $max) {
        $this->values[$id] = [
            'min' => $min,
            'max' => $max
        ];
        return $this;
    }

    /**
     * @return array[int]array[string]int
     */
    public function getValues(): array {
        return $this->values;
    }

    /**
     * @param WeaponStat $weaponStat
     * @return array[string]int|null
     */
    public function getValue(WeaponStat $weaponStat) {
        if (array_key_exists($weaponStat->getID(), $this->values)) {
            return $this->values[$weaponStat->getID()];
        }
        return null;
    }

}