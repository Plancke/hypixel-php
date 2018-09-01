<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds\weapon;

class WeaponInventory {

    protected $weapons = [
        Rarity::COMMON => [],
        Rarity::RARE => [],
        Rarity::EPIC => [],
        Rarity::LEGENDARY => []
    ];
    protected $weaponsById = [];

    public function __construct($weapons) {
        foreach ($weapons as $weaponRaw) {
            $weapon = new Weapon($weaponRaw);
            array_push($this->weapons[$weapon->getCategory()], $weapon);
            $this->weaponsById[$weapon->getID()] = $weapon;
        }
    }

    /**
     * @return array[string]Weapon[]
     */
    public function getWeapons() {
        return $this->weapons;
    }

    public function getWeapon($id) {
        return array_key_exists($id, $this->weaponsById) ? $this->weaponsById[$id] : null;
    }

}
