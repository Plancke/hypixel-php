<?php

namespace Plancke\HypixelPHP\wrappers;

/**
 * Class Weapon
 * @author Plancke, LCastr0
 * @version 1.0.0
 *
 * HOW DOES IT WORK
 * ----------------
 *
 * 1. Weapon Prefix:
 * @see{Weapon::getPrefix()}
 * The prefix is determined by the overall score. Lets take a Legendary weapon as an example.
 * We need to know 3 values to start off. MIN, MAX, and PREFIX_COUNT.
 * MIN, MAX are the scores a legendary has at least/most respectively.
 * PREFIX_COUNT is the amount of prefixes for that Weapon category
 *
 * (MAX - MIN) / PREFIX_COUNT = DIFF
 *
 * DIFF now means the values of a 'slice' in between those MIN/MAX values.
 * To get the prefix we can now get the weapon's TOTAL_SCORE and the corresponding slice will be the weapon's prefix.
 *
 *
 * 2. Material Name
 * @see{Weapon::getMaterialName()}
 * The Material Name works via a Map which converts the Bukkit MaterialType into the displayed name.
 *
 *
 * 3. Spec Name
 * The weapon array contains a sub-array which contains a 'spec' and 'playerClass' field.
 * Together these form up the spec name.
 * playerClass is the zero-based index of the multiple classes @see{PlayerClasses}.
 * spec is the zero-based index of the multiple spec for that class @see{PlayerClass}.
 *
 *
 * 4. Ability Name
 * The weapon array contains an 'ability' field. that field refers to the
 * zero-based index of the ability according to the location in your hotbar.
 * combine this mapping with the Spec to get the name.
 *
 */
class Weapon {
    private $WEAPON;

    private $scores = [
        Rarity::COMMON => ['min' => 276, 'max' => 428],
        Rarity::RARE => ['min' => 359, 'max' => 521],
        Rarity::EPIC => ['min' => 450, 'max' => 604],
        Rarity::LEGENDARY => ['min' => 595, 'max' => 805]
    ];
    private $prefixes = [
        Rarity::COMMON => [
            "Crumbly", "Flimsy", "Rough", "Honed", "Refined", "Balanced"
        ],
        Rarity::RARE => [
            "Savage", "Vicious", "Deadly", "Perfect"
        ],
        Rarity::EPIC => [
            "Fierce", "Mighty", "Brutal", "Gladiator's"
        ],
        Rarity::LEGENDARY => [
            "Vanquisher's", "Champion's", "Warlord's"
        ]
    ];
    private $materialMap = [
        'WOOD_AXE' => 'Steel Sword', 'STONE_AXE' => 'Training Sword', 'IRON_AXE' => 'Demonblade',
        'GOLD_AXE' => 'Venomstrike', 'DIAMOND_AXE' => 'Diamondspark', 'WOOD_HOE' => 'Zweireaper',
        'STONE_HOE' => 'Runeblade', 'IRON_HOE' => 'Elven Greatsword', 'GOLD_HOE' => 'Hatchet',
        'DIAMOND_HOE' => 'Gem Axe', 'WOOD_SPADE' => 'Nomegusta', 'STONE_SPADE' => 'Drakefang',
        'IRON_SPADE' => 'Hammer', 'GOLD_SPADE' => 'Stone Mallet', 'DIAMOND_SPADE' => 'Gemcrusher',
        'WOOD_PICKAXE' => 'Abbadon', 'STONE_PICKAXE' => 'Walking Stick', 'IRON_PICKAXE' => 'World Tree Branch',
        'GOLD_PICKAXE' => 'Flameweaver', 'DIAMOND_PICKAXE' => 'Void Twig', 'SALMON' => 'Scimitar',
        'PUFFERFISH' => 'Golden Gladius', 'CLOWNFISH' => 'Magmasword', 'COD' => 'Frostbite',
        'ROTTEN_FLESH' => 'Pike', 'POTATO' => 'Halberd', 'MELON' => 'Divine Reach',
        'POISONOUS_POTATO' => 'Ruby Thorn', 'STRING' => 'Hammer of Light', 'RAW_CHICKEN' => 'Nethersteel Katana',
        'MUTTON' => 'Claws', 'PORK' => 'Mandibles', 'RAW_BEEF' => 'Katar',
        'APPLE' => 'Enderfist', 'PUMPKIN_PIE' => 'Orc Axe', 'COOKED_COD' => 'Doubleaxe',
        'BREAD' => 'Runic Axe', 'MUSHROOM_STEW' => 'Lunar Relic', 'RABBIT_STEW' => 'Bludgeon',
        'COOKED_RABBIT' => 'Cudgel', 'COOKED_CHICKEN' => 'Tenderizer', 'BAKED_POTATO' => 'Broccomace',
        'COOKED_SALMON' => 'Felflame Blade', 'COOKED_MUTTON' => 'Amaranth', 'COOKED_BEEF' => 'Armblade',
        'GRILLED_PORK' => 'Gemini', 'COOKED_PORKCHOP' => 'Gemini', 'GOLDEN_CARROT' => 'Void Edge'
    ];
    private $colors = [
        Rarity::COMMON => '§a',
        Rarity::RARE => '§9',
        Rarity::EPIC => '§5',
        Rarity::LEGENDARY => '§6'
    ];
    private $abilities = [];
    private $forced_upgrade_level = -1;

    function __construct($WEAPON) {
        $this->WEAPON = $WEAPON;

        /* Load abilities */
        // Mage
        $this->addAbility(0, 0, new Ability("Fireball", "Pyromancer", AbilityType::DAMAGE));
        $this->addAbility(0, 0, new Ability("Frostbolt", "Cryomancer", AbilityType::DAMAGE));
        $this->addAbility(0, 0, new Ability("Water Bolt", "Aquamancer", AbilityType::HEAL));
        $this->addAbility(0, 1, new Ability("Flame Burst", "Pyromancer", AbilityType::DAMAGE));
        $this->addAbility(0, 1, new Ability("Freezing Breath", "Cryomancer", AbilityType::DAMAGE));
        $this->addAbility(0, 1, new Ability("Water Breath", "Aquamancer", AbilityType::HEAL));
        // Warrior
        $this->addAbility(1, 0, new Ability("Wounding Strike", "Berserker", AbilityType::DAMAGE));
        $this->addAbility(1, 0, new Ability("Wounding Strike", "Defender", AbilityType::DAMAGE));
        $this->addAbility(1, 1, new Ability("Seismic Wave", "Berserker", AbilityType::DAMAGE));
        $this->addAbility(1, 1, new Ability("Seismic Wave", "Defender", AbilityType::DAMAGE));
        $this->addAbility(1, 2, new Ability("Ground Slam", "Berserker", AbilityType::DAMAGE));
        $this->addAbility(1, 2, new Ability("Ground Slam", "Defender", AbilityType::DAMAGE));
        // Paladin
        $this->addAbility(2, 0, new Ability("Avenger's Strike", "Avenger", AbilityType::DAMAGE));
        $this->addAbility(2, 0, new Ability("Crusader's Strike", "Crusader", AbilityType::DAMAGE));
        $this->addAbility(2, 3, new Ability("Holy Radiance", "Protector", AbilityType::HEAL));
        $this->addAbility(2, 1, new Ability("Consecrate", "Avenger", AbilityType::DAMAGE));
        $this->addAbility(2, 1, new Ability("Consecrate", "Crusader", AbilityType::DAMAGE));
        $this->addAbility(2, 4, new Ability("Hammer of Light", "Protector", AbilityType::HEAL));
        // Shaman
        $this->addAbility(3, 0, new Ability("Lightning Bolt", "Thunderlord", AbilityType::DAMAGE));
        $this->addAbility(3, 0, new Ability("Earthen Spike", "Earthwarden", AbilityType::DAMAGE));
        $this->addAbility(3, 1, new Ability("Chain Lightning", "Thunderlord", AbilityType::DAMAGE));
        $this->addAbility(3, 1, new Ability("Boulder", "Earthwarden", AbilityType::DAMAGE));
        $this->addAbility(3, 2, new Ability("Windfury", "Thunderlord", AbilityType::DAMAGE));
        $this->addAbility(3, 3, new Ability("Chain Healing", "Earthwarden", AbilityType::HEAL));
    }

    private function addAbility($class, $slot, $ability) {
        if (!isset($this->abilities[$class])) {
            $this->abilities[$class] = [];
        }
        if (!isset($this->abilities[$class][$slot])) {
            $this->abilities[$class][$slot] = [];
        }
        array_push($this->abilities[$class][$slot], $ability);
    }

    function setForcedUpgradeLevel($level) {
        $this->forced_upgrade_level = $level;
    }

    function getMinMaxDamage() {
        $dmg = $this->getStatById(WeaponStats::DAMAGE);
        $fifteen = $dmg * 0.15;
        return [$dmg - $fifteen, $dmg + $fifteen];
    }

    function getStatById($stat) {
        $weaponStat = WeaponStats::fromID($stat);
        return $weaponStat != null ? $this->getStat($weaponStat) : 0;
    }

    /**
     * @param WeaponStat $stat
     * @return int
     */
    function getStat($stat) {
        $val = $this->getField($stat->getField());
        $val *= 1 + ($this->getUpgradeAmount() * $stat->getUpgrade() / 100);
        return $val;
    }

    function getField($key, $def = 0) {
        return isset($this->WEAPON[$key]) ? $this->WEAPON[$key] : $def;
    }

    function getUpgradeAmount() {
        if ($this->isForcedUpgrade()) {
            return min($this->forced_upgrade_level, $this->getMaxUpgrades());
        }
        return $this->getField('upgradeTimes');
    }

    function isForcedUpgrade() {
        return $this->forced_upgrade_level >= 0;
    }

    function getMaxUpgrades() {
        return $this->getField('upgradeMax');
    }

    function isCrafted() {
        return isset($this->WEAPON['crafted']) ? $this->WEAPON['crafted'] : false;
    }

    function getName() {
        $prefix = $this->getPrefix();
        $material = $this->getMaterialName();
        $specialization = $this->getPlayerClass()->getSpec()->getName();
        return $prefix . " " . $material . " of the " . $specialization;
    }

    function getPrefix() {
        $names = $this->prefixes[$this->getCategory()];
        $namesInt = intval(count($names));

        $score = $this->getScore();
        $diff = ($this->getMaxScore() - $this->getMinScore()) / sizeof($this->prefixes[$this->getCategory()]);

        for ($i = 0; $i < $namesInt; $i++) {
            $left = $this->getMinScore() + $diff * ($i + 1);
            if ($score <= $left) {
                return $names[$i];
            }
        }

        return end($names);
    }

    function getCategory() {
        return $this->WEAPON['category'];
    }

    function getScore() {
        $score = 0;
        foreach (WeaponStats::values() as $stat) {
            $score += $this->getStatById($stat);
        }
        return $score;
    }

    function getMaxScore() {
        return $this->scores[$this->getCategory()]['max'];
    }

    function getMinScore() {
        return $this->scores[$this->getCategory()]['min'];
    }

    function getMaterialName() {
        return isset($this->materialMap[$this->getMaterial()]) ? $this->materialMap[$this->getMaterial()] : $this->getMaterial();
    }

    function getMaterial() {
        return $this->WEAPON['material'];
    }

    function getPlayerClass() {
        return PlayerClasses::fromID($this->WEAPON['spec']['playerClass'], $this->WEAPON['spec']['spec']);
    }

    function getColor() {
        return $this->colors[$this->getCategory()];
    }

    function getID() {
        return $this->WEAPON['id'];
    }

    /**
     * @return Ability|null
     */
    function getAbility() {
        $ABILITIES = $this->abilities[$this->getPlayerClass()->getID()][$this->WEAPON['ability']];
        foreach ($ABILITIES as $ABILITY) {
            /* @var $ABILITY Ability */
            if ($ABILITY->getSpec() == $this->getPlayerClass()->getSpec()->getName()) {
                return $ABILITY;
            }
        }
        return null;
    }

    function isUnlocked() {
        return isset($this->WEAPON['unlocked']) ? $this->WEAPON['unlocked'] : false;
    }

}

class Rarity {
    const COMMON = "COMMON";
    const RARE = "RARE";
    const EPIC = "EPIC";
    const LEGENDARY = "LEGENDARY";
}

class AbilityType {
    const HEAL = "HEAL";
    const DAMAGE = "DAMAGE";
}

class WeaponStats {
    const DAMAGE = 0;
    const CHANCE = 1;
    const MULTIPLIER = 2;
    const ABILITY_BOOST = 3;
    const HEALTH = 4;
    const ENERGY = 5;
    const COOLDOWN = 6;
    const MOVEMENT = 7;

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
     * @deprecated
     */
    public static function getAllTypes() {
        return self::values();
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

class WeaponStat {
    private $name, $field, $upgrade, $id;

    function __construct($name, $field, $upgrade, $id) {
        $this->name = $name;
        $this->field = $field;
        $this->upgrade = $upgrade;
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function getField() {
        return $this->field;
    }

    public function getUpgrade() {
        return $this->upgrade;
    }

    public function getID() {
        return $this->id;
    }
}

class PlayerClasses {
    const MAGE = 0;
    const WARRIOR = 1;
    const PALADIN = 2;
    const SHAMAN = 3;

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

class PlayerClass {
    private $name, $spec, $id;

    private $specs = [
        PlayerClasses::MAGE => [0 => "Pyromancer", 1 => "Cryomancer", 2 => "Aquamancer"],
        PlayerClasses::WARRIOR => [0 => "Berserker", 1 => "Defender"],
        PlayerClasses::PALADIN => [0 => "Avenger", 1 => "Crusader", 2 => "Protector"],
        PlayerClasses::SHAMAN => [0 => "Thunderlord", 1 => "Earthwarden"],
    ];

    function __construct($name, $spec, $id) {
        $this->name = $name;
        $this->spec = $spec;
        $this->id = $id;
    }

    function getID() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getDisplay() {
        return ucfirst($this->name);
    }

    function getSpec() {
        return new Spec($this->specs[$this->id][$this->spec], $this->spec);
    }
}

class Spec {
    private $name, $id;

    function __construct($name, $id) {
        $this->name = $name;
        $this->id = $id;
    }

    function getName() {
        return $this->name;
    }

    function getID() {
        return $this->id;
    }
}

class Ability {
    private $name, $type, $spec;

    function __construct($name, $spec, $type) {
        $this->name = $name;
        $this->spec = $spec;
        $this->type = $type;
    }

    function getName() {
        return $this->name;
    }

    function getSpec() {
        return $this->spec;
    }

    function getType() {
        return $this->type;
    }
}