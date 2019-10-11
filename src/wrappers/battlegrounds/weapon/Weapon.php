<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds\weapon;

use Plancke\HypixelPHP\color\ColorUtils;
use Plancke\HypixelPHP\wrappers\battlegrounds\Abilities;
use Plancke\HypixelPHP\wrappers\battlegrounds\Ability;
use Plancke\HypixelPHP\wrappers\battlegrounds\PlayerClass;
use Plancke\HypixelPHP\wrappers\battlegrounds\PlayerClasses;

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

    protected static $scores = [
        Rarity::COMMON => ['min' => 276, 'max' => 428],
        Rarity::RARE => ['min' => 359, 'max' => 521],
        Rarity::EPIC => ['min' => 450, 'max' => 604],
        Rarity::LEGENDARY => ['min' => 595, 'max' => 805]
    ];
    protected static $prefixes = [
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
    protected static /** @noinspection SpellCheckingInspection */
        $materialMap = [
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
    protected static $colors = [
        Rarity::COMMON => ColorUtils::GREEN,
        Rarity::RARE => ColorUtils::BLUE,
        Rarity::EPIC => ColorUtils::DARK_PURPLE,
        Rarity::LEGENDARY => ColorUtils::GOLD
    ];

    protected $weapon;
    protected $forcedUpgradeLevel = -1;

    /**
     * Weapon constructor.
     * @param array $WEAPON
     */
    function __construct($WEAPON) {
        $this->weapon = $WEAPON;
    }

    /**
     * @return array
     */
    public function getMinMaxDamage() {
        $dmg = $this->getStatById(WeaponStats::DAMAGE);
        $fifteen = $dmg * 0.15;
        return [$dmg - $fifteen, $dmg + $fifteen];
    }

    /**
     * @param $stat
     * @return int
     */
    public function getStatById($stat) {
        $weaponStat = WeaponStats::fromID($stat);
        return $weaponStat != null ? $this->getStat($weaponStat) : 0;
    }

    /**
     * @param WeaponStat $stat
     * @return int
     */
    public function getStat($stat) {
        $val = $this->getBaseStat($stat);
        $val *= 1 + ($this->getUpgradeAmount() * $stat->getUpgrade() / 100);
        return $val;
    }

    /**
     * @param WeaponStat $stat
     * @return int
     */
    public function getBaseStat($stat) {
        return $this->getField($stat->getField());
    }

    /**
     * @param $key
     * @param int $def
     * @return int
     */
    public function getField($key, $def = 0) {
        return array_key_exists($key, $this->weapon) ? $this->weapon[$key] : $def;
    }

    /**
     * @return int|mixed
     */
    public function getUpgradeAmount() {
        if ($this->isForcedUpgrade()) {
            return min($this->forcedUpgradeLevel, $this->getMaxUpgrades());
        }
        return $this->getField('upgradeTimes');
    }

    /**
     * @return bool
     */
    public function isForcedUpgrade() {
        return $this->forcedUpgradeLevel >= 0;
    }

    /**
     * @return int
     */
    public function getMaxUpgrades() {
        return $this->getField('upgradeMax');
    }

    /**
     * @return int
     */
    public function getForcedUpgradeLevel() {
        return $this->forcedUpgradeLevel;
    }

    /**
     * @param $level
     */
    public function setForcedUpgradeLevel($level) {
        $this->forcedUpgradeLevel = $level;
    }

    /**
     * @return bool
     */
    public function isCrafted() {
        return array_key_exists('crafted', $this->weapon) ? $this->weapon['crafted'] : false;
    }

    /**
     * @return string
     */
    public function getName() {
        $prefix = $this->getPrefix();
        $material = $this->getMaterialName();
        $specialization = $this->getPlayerClass()->getSpec()->getName();
        return $prefix . " " . $material . " of the " . $specialization;
    }

    /**
     * @return mixed
     */
    public function getPrefix() {
        $names = Weapon::$prefixes[$this->getCategory()];
        $namesInt = intval(count($names));

        $score = $this->getScore();
        $diff = ($this->getMaxScore() - $this->getMinScore()) / sizeof(Weapon::$prefixes[$this->getCategory()]);

        for ($i = 0; $i < $namesInt; $i++) {
            $left = $this->getMinScore() + $diff * ($i + 1);
            if ($score <= $left) {
                return $names[$i];
            }
        }

        return end($names);
    }

    /**
     * @return string
     */
    public function getCategory() {
        return $this->weapon['category'];
    }

    /**
     * @return int
     */
    public function getScore() {
        $score = 0;
        foreach (WeaponStats::values() as $stat) {
            $score += $this->getStatById($stat);
        }
        return $score;
    }

    /**
     * @return int
     */
    public function getMaxScore() {
        return Weapon::$scores[$this->getCategory()]['max'];
    }

    /**
     * @return int
     */
    public function getMinScore() {
        return Weapon::$scores[$this->getCategory()]['min'];
    }

    /**
     * @return string
     */
    public function getMaterialName() {
        return array_key_exists($this->getMaterial(), Weapon::$materialMap) ? Weapon::$materialMap[$this->getMaterial()] : $this->getMaterial();
    }

    /**
     * @return string
     */
    public function getMaterial() {
        return $this->weapon['material'];
    }

    /**
     * @return PlayerClass|null
     */
    public function getPlayerClass() {
        return PlayerClasses::fromID($this->weapon['spec']['playerClass'], $this->weapon['spec']['spec']);
    }

    public function isPerfect() {
        return WeaponGrader::getGrade($this) == 1;
    }

    /**
     * @return string
     */
    public function getColor() {
        return Weapon::$colors[$this->getCategory()];
    }

    /**
     * @return string
     */
    public function getID() {
        return $this->weapon['id'];
    }

    /**
     * @return Ability|null
     */
    public function getAbility() {
        $ABILITIES = Abilities::getAbilities($this->getPlayerClass()->getID())[$this->weapon['ability']];
        foreach ($ABILITIES as $ABILITY) {
            /* @var $ABILITY Ability */
            if ($ABILITY->getSpec() == $this->getPlayerClass()->getSpec()->getName()) {
                return $ABILITY;
            }
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isUnlocked() {
        return array_key_exists('unlocked', $this->weapon) ? $this->weapon['unlocked'] : false;
    }

    public function getRaw() {
        return $this->weapon;
    }

}
