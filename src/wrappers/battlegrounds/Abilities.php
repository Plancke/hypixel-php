<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds;

use Plancke\HypixelPHP\wrappers\battlegrounds\weapon\AbilityType;

class Abilities {

    protected static $abilities = null;

    public static function getAbilities(int $id) {
        Abilities::initAbilities();

        return Abilities::$abilities[$id];
    }

    protected static function initAbilities() {
        if (Abilities::$abilities != null) return;

        Abilities::$abilities = [];

        // Mage
        Abilities::addAbility(0, 0, new Ability("Fireball", "Pyromancer", AbilityType::DAMAGE));
        Abilities::addAbility(0, 0, new Ability("Frostbolt", "Cryomancer", AbilityType::DAMAGE));
        Abilities::addAbility(0, 0, new Ability("Water Bolt", "Aquamancer", AbilityType::HEAL));
        Abilities::addAbility(0, 1, new Ability("Flame Burst", "Pyromancer", AbilityType::DAMAGE));
        Abilities::addAbility(0, 1, new Ability("Freezing Breath", "Cryomancer", AbilityType::DAMAGE));
        Abilities::addAbility(0, 1, new Ability("Water Breath", "Aquamancer", AbilityType::HEAL));
        // Warrior
        Abilities::addAbility(1, 0, new Ability("Wounding Strike", "Berserker", AbilityType::DAMAGE));
        Abilities::addAbility(1, 0, new Ability("Wounding Strike", "Defender", AbilityType::DAMAGE));
        Abilities::addAbility(1, 1, new Ability("Seismic Wave", "Berserker", AbilityType::DAMAGE));
        Abilities::addAbility(1, 1, new Ability("Seismic Wave", "Defender", AbilityType::DAMAGE));
        Abilities::addAbility(1, 2, new Ability("Ground Slam", "Berserker", AbilityType::DAMAGE));
        Abilities::addAbility(1, 2, new Ability("Ground Slam", "Defender", AbilityType::DAMAGE));
        // Paladin
        Abilities::addAbility(2, 0, new Ability("Avenger's Strike", "Avenger", AbilityType::DAMAGE));
        Abilities::addAbility(2, 0, new Ability("Crusader's Strike", "Crusader", AbilityType::DAMAGE));
        Abilities::addAbility(2, 3, new Ability("Holy Radiance", "Protector", AbilityType::HEAL));
        Abilities::addAbility(2, 1, new Ability("Consecrate", "Avenger", AbilityType::DAMAGE));
        Abilities::addAbility(2, 1, new Ability("Consecrate", "Crusader", AbilityType::DAMAGE));
        Abilities::addAbility(2, 4, new Ability("Hammer of Light", "Protector", AbilityType::HEAL));
        // Shaman
        Abilities::addAbility(3, 0, new Ability("Lightning Bolt", "Thunderlord", AbilityType::DAMAGE));
        Abilities::addAbility(3, 0, new Ability("Earthen Spike", "Earthwarden", AbilityType::DAMAGE));
        Abilities::addAbility(3, 1, new Ability("Chain Lightning", "Thunderlord", AbilityType::DAMAGE));
        Abilities::addAbility(3, 1, new Ability("Boulder", "Earthwarden", AbilityType::DAMAGE));
        Abilities::addAbility(3, 2, new Ability("Windfury", "Thunderlord", AbilityType::DAMAGE));
        Abilities::addAbility(3, 3, new Ability("Chain Healing", "Earthwarden", AbilityType::HEAL));
    }

    /**
     * @param $class
     * @param $slot
     * @param $ability
     */
    protected static function addAbility($class, $slot, $ability) {
        if (!array_key_exists($class, Abilities::$abilities)) Abilities::$abilities[$class] = [];
        if (!array_key_exists($slot, Abilities::$abilities[$class])) Abilities::$abilities[$class][$slot] = [];
        array_push(Abilities::$abilities[$class][$slot], $ability);
    }


}