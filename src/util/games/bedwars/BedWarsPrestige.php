<?php

namespace Plancke\HypixelPHP\util\games\bedwars;

use Plancke\HypixelPHP\color\ColorUtils;

/**
 * Class BedWarsPrestige
 * @package Plancke\HypixelPHP\util\games\bedwars
 */
class BedWarsPrestige {

    const NONE = 0;
    const IRON = 1;
    const GOLD = 2;
    const DIAMOND = 3;
    const EMERALD = 4;
    const SAPPHIRE = 5;
    const RUBY = 6;
    const CRYSTAL = 7;
    const OPAL = 8;
    const AMETHYST = 9;
    const RAINBOW = 10;
    const IRON_PRIME = 11;
    const GOLD_PRIME = 12;
    const DIAMOND_PRIME = 13;
    const EMERALD_PRIME = 14;
    const SAPPHIRE_PRIME = 15;
    const RUBY_PRIME = 16;
    const CRYSTAL_PRIME = 17;
    const OPAL_PRIME = 18;
    const AMETHYST_PRIME = 19;
    const MIRROR = 20;
    const LIGHT = 21;
    const DAWN = 22;
    const DUSK = 23;
    const AIR = 24;
    const WIND = 25;
    const NEBULA = 26;
    const THUNDER = 27;
    const EARTH = 28;
    const WATER = 29;
    const FIRE = 30;
    const SUNRISE = 31;
    const ECLIPSE = 32;
    const GAMMA = 33;
    const MAJESTIC = 34;
    const ANDESINE = 35;
    const MARINE = 36;
    const ELEMENT = 37;
    const GALAXY = 38;
    const ATOMIC = 39;
    const SUNSET = 40;
    const TIME = 41;
    const WINTER = 42;
    const OBSIDIAN = 43;
    const SPRING = 44;
    const ICE = 45;
    const SUMMER = 46;
    const SPINEL = 47;
    const AUTUMN = 48;
    const MYSTIC = 49;
    const ETERNAL = 50;

    const HIGHEST_PRESTIGE = self::ETERNAL;

    private static $cache = [];

    protected $ordinal;
    protected $name;
    protected $colors;

    /**
     * BedWarsPrestige constructor.
     * @param $ordinal
     * @param $name
     * @param $colors
     */
    public function __construct($ordinal, $name, $colors) {
        $this->ordinal = $ordinal;
        $this->name = $name;
        $this->colors = $colors;
    }

    /**
     * @return array
     */
    public static function values(): array {
        return [
            self::NONE,
            self::IRON,
            self::GOLD,
            self::DIAMOND,
            self::EMERALD,
            self::SAPPHIRE,
            self::CRYSTAL,
            self::OPAL,
            self::AMETHYST,
            self::RAINBOW,
            self:: IRON_PRIME,
            self::GOLD_PRIME,
            self::DIAMOND_PRIME,
            self::EMERALD_PRIME,
            self::SAPPHIRE_PRIME,
            self::RUBY_PRIME,
            self::CRYSTAL_PRIME,
            self::OPAL_PRIME,
            self::AMETHYST_PRIME,
            self::MIRROR,
            self::LIGHT,
            self::DAWN,
            self::DUSK,
            self::AIR,
            self::WIND,
            self::NEBULA,
            self::THUNDER,
            self::EARTH,
            self::WATER,
            self::FIRE,
            self::SUNRISE,
            self::ECLIPSE,
            self::GAMMA,
            self::MAJESTIC,
            self::ANDESINE,
            self::MARINE,
            self::ELEMENT,
            self::GALAXY,
            self::ATOMIC,
            self::SUNSET,
            self::TIME,
            self::WINTER,
            self::OBSIDIAN,
            self::SPRING,
            self::ICE,
            self::SUMMER,
            self::SPINEL,
            self::AUTUMN,
            self::MYSTIC,
            self::ETERNAL
        ];
    }

    /**
     * @param $id
     *
     * @return BedWarsPrestige|null
     */
    public static function fromID($id) {
        if (!isset(BedWarsPrestige::$cache[$id])) {
            BedWarsPrestige::$cache[$id] = BedWarsPrestige::fromID0($id);
        }
        return BedWarsPrestige::$cache[$id];
    }

    /**
     * @param $id
     *
     * @return BedWarsPrestige|null
     */
    private static function fromID0($id) {
        switch ($id) {
            case self::NONE:
                return new self($id, "None", [ColorUtils::GRAY]);
            case self::IRON:
                return new self($id, "Iron", [ColorUtils::WHITE]);
            case self::GOLD:
                return new self($id, "Gold", [ColorUtils::GOLD]);
            case self::DIAMOND:
                return new self($id, "Diamond", [ColorUtils::AQUA]);
            case self::EMERALD:
                return new self($id, "Emerald", [ColorUtils::DARK_GREEN]);
            case self::SAPPHIRE:
                return new self($id, "Sapphire", [ColorUtils::DARK_AQUA]);
            case self::RUBY:
                return new self($id, "Ruby", [ColorUtils::DARK_RED]);
            case self::CRYSTAL:
                return new self($id, "Crystal", [ColorUtils::LIGHT_PURPLE]);
            case self::OPAL:
                return new self($id, "Opal", [ColorUtils::BLUE]);
            case self::AMETHYST:
                return new self($id, "Amethyst", [ColorUtils::DARK_PURPLE]);
            case self::RAINBOW:
                return new self($id, "Rainbow", [
                    ColorUtils::RED, ColorUtils::GOLD, ColorUtils::YELLOW, ColorUtils::GREEN,
                    ColorUtils::AQUA, ColorUtils::LIGHT_PURPLE, ColorUtils::DARK_PURPLE
                ]);
            case self::IRON_PRIME:
                return new self($id, "Iron Prime", [
                    ColorUtils::GRAY, ColorUtils::WHITE, ColorUtils::WHITE,
                    ColorUtils::WHITE, ColorUtils::WHITE, ColorUtils::GRAY, ColorUtils::GRAY
                ]);
            case self::GOLD_PRIME:
                return new self($id, "Gold Prime", [
                    ColorUtils::GRAY, ColorUtils::YELLOW, ColorUtils::YELLOW,
                    ColorUtils::YELLOW, ColorUtils::YELLOW, ColorUtils::GOLD, ColorUtils::GRAY
                ]);
            case self::DIAMOND_PRIME:
                return new self($id, "Diamond Prime", [
                    ColorUtils::GRAY, ColorUtils::AQUA, ColorUtils::AQUA,
                    ColorUtils::AQUA, ColorUtils::AQUA, ColorUtils::DARK_AQUA, ColorUtils::GRAY
                ]);
            case self::EMERALD_PRIME:
                return new self($id, "Emerald Prime", [
                    ColorUtils::GRAY, ColorUtils::GREEN, ColorUtils::GREEN,
                    ColorUtils::GREEN, ColorUtils::GREEN, ColorUtils::DARK_GREEN, ColorUtils::GRAY
                ]);
            case self::SAPPHIRE_PRIME:
                return new self($id, "Sapphire Prime", [
                    ColorUtils::GRAY, ColorUtils::DARK_AQUA, ColorUtils::DARK_AQUA,
                    ColorUtils::DARK_AQUA, ColorUtils::DARK_AQUA, ColorUtils::BLUE, ColorUtils::GRAY
                ]);
            case self::RUBY_PRIME:
                return new self($id, "Ruby Prime", [
                    ColorUtils::GRAY, ColorUtils::RED, ColorUtils::RED,
                    ColorUtils::RED, ColorUtils::RED, ColorUtils::DARK_RED, ColorUtils::GRAY
                ]);
            case self::CRYSTAL_PRIME:
                return new self($id, "Crystal Prime", [
                    ColorUtils::GRAY, ColorUtils::LIGHT_PURPLE, ColorUtils::LIGHT_PURPLE,
                    ColorUtils::LIGHT_PURPLE, ColorUtils::LIGHT_PURPLE, ColorUtils::DARK_PURPLE, ColorUtils::GRAY
                ]);
            case self::OPAL_PRIME:
                return new self($id, "Opal Prime", [
                    ColorUtils::GRAY, ColorUtils::BLUE, ColorUtils::BLUE,
                    ColorUtils::BLUE, ColorUtils::BLUE, ColorUtils::DARK_BLUE, ColorUtils::GRAY
                ]);
            case self::AMETHYST_PRIME:
                return new self($id, "Amethyst Prime", [
                    ColorUtils::GRAY, ColorUtils::DARK_PURPLE, ColorUtils::DARK_PURPLE,
                    ColorUtils::DARK_PURPLE, ColorUtils::DARK_PURPLE, ColorUtils::DARK_GRAY, ColorUtils::GRAY
                ]);
            case self::MIRROR:
                return new self($id, "Mirror", [
                    ColorUtils::DARK_GRAY, ColorUtils::GRAY, ColorUtils::WHITE,
                    ColorUtils::WHITE, ColorUtils::GRAY, ColorUtils::GRAY, ColorUtils::DARK_GRAY
                ]);
            case self::LIGHT:
                return new self($id, "Light", [
                    ColorUtils::WHITE, ColorUtils::WHITE, ColorUtils::YELLOW,
                    ColorUtils::YELLOW, ColorUtils::GOLD, ColorUtils::GOLD, ColorUtils::GOLD
                ]);
            case self::DAWN:
                return new self($id, "Dawn", [
                    ColorUtils::GOLD, ColorUtils::GOLD, ColorUtils::WHITE,
                    ColorUtils::WHITE, ColorUtils::AQUA, ColorUtils::DARK_AQUA, ColorUtils::DARK_AQUA
                ]);
            case self::DUSK:
                return new self($id, "Dusk", [
                    ColorUtils::DARK_PURPLE, ColorUtils::DARK_PURPLE, ColorUtils::LIGHT_PURPLE,
                    ColorUtils::LIGHT_PURPLE, ColorUtils::GOLD, ColorUtils::YELLOW, ColorUtils::YELLOW
                ]);
            case self::AIR:
                return new self($id, "Air", [
                    ColorUtils::AQUA, ColorUtils::AQUA, ColorUtils::WHITE,
                    ColorUtils::WHITE, ColorUtils::GRAY, ColorUtils::GRAY, ColorUtils::DARK_GRAY
                ]);
            case self::WIND:
                return new self($id, "Wind", [
                    ColorUtils::WHITE, ColorUtils::WHITE, ColorUtils::GREEN,
                    ColorUtils::GREEN, ColorUtils::DARK_GREEN, ColorUtils::DARK_GREEN, ColorUtils::DARK_GREEN
                ]);
            case self::NEBULA:
                return new self($id, "Nebula", [
                    ColorUtils::DARK_RED, ColorUtils::DARK_RED, ColorUtils::RED,
                    ColorUtils::RED, ColorUtils::LIGHT_PURPLE, ColorUtils::LIGHT_PURPLE, ColorUtils::DARK_PURPLE
                ]);
            case self::THUNDER:
                return new self($id, "Thunder", [
                    ColorUtils::YELLOW, ColorUtils::YELLOW, ColorUtils::WHITE,
                    ColorUtils::WHITE, ColorUtils::DARK_GRAY, ColorUtils::DARK_GRAY, ColorUtils::DARK_GRAY
                ]);
            case self::EARTH:
                return new self($id, "Earth", [
                    ColorUtils::GREEN, ColorUtils::GREEN, ColorUtils::DARK_GREEN,
                    ColorUtils::DARK_GREEN, ColorUtils::GOLD, ColorUtils::GOLD, ColorUtils::YELLOW
                ]);
            case self::WATER:
                return new self($id, "Water", [
                    ColorUtils::AQUA, ColorUtils::AQUA, ColorUtils::DARK_AQUA,
                    ColorUtils::DARK_AQUA, ColorUtils::BLUE, ColorUtils::BLUE, ColorUtils::DARK_BLUE
                ]);
            case self::FIRE:
                return new self($id, "Fire", [
                    ColorUtils::YELLOW, ColorUtils::YELLOW, ColorUtils::GOLD,
                    ColorUtils::GOLD, ColorUtils::RED, ColorUtils::RED, ColorUtils::DARK_RED
                ]);
            case self::SUNRISE:
                return new self($id, "Sunrise", [
                    ColorUtils::BLUE, ColorUtils::BLUE, ColorUtils::DARK_AQUA,
                    ColorUtils::DARK_AQUA, ColorUtils::GOLD, ColorUtils::GOLD, ColorUtils::YELLOW
                ]);
            case self::ECLIPSE:
                return new self($id, "Eclipse", [
                    ColorUtils::RED, ColorUtils::DARK_RED, ColorUtils::GRAY,
                    ColorUtils::GRAY, ColorUtils::DARK_RED, ColorUtils::RED, ColorUtils::RED
                ]);
            case self::GAMMA:
                return new self($id, "Gamma", [
                    ColorUtils::BLUE, ColorUtils::BLUE, ColorUtils::BLUE,
                    ColorUtils::LIGHT_PURPLE, ColorUtils::RED, ColorUtils::RED, ColorUtils::DARK_RED
                ]);
            case self::MAJESTIC:
                return new self($id, "Majestic", [
                    ColorUtils::DARK_GREEN, ColorUtils::GREEN, ColorUtils::LIGHT_PURPLE,
                    ColorUtils::LIGHT_PURPLE, ColorUtils::DARK_PURPLE, ColorUtils::DARK_PURPLE
                ]);
            case self::ANDESINE:
                return new self($id, "Andesine", [
                    ColorUtils::RED, ColorUtils::RED, ColorUtils::DARK_RED,
                    ColorUtils::DARK_RED, ColorUtils::DARK_GREEN, ColorUtils::GREEN, ColorUtils::GREEN
                ]);
            case self::MARINE:
                return new self($id, "Marine", [
                    ColorUtils::GREEN, ColorUtils::GREEN, ColorUtils::GREEN,
                    ColorUtils::AQUA, ColorUtils::BLUE, ColorUtils::BLUE, ColorUtils::DARK_BLUE
                ]);
            case self::ELEMENT:
                return new self($id, "Element", [
                    ColorUtils::DARK_RED, ColorUtils::DARK_RED, ColorUtils::RED,
                    ColorUtils::RED, ColorUtils::AQUA, ColorUtils::DARK_AQUA, ColorUtils::DARK_AQUA
                ]);
            case self::GALAXY:
                return new self($id, "Galaxy", [
                    ColorUtils::DARK_BLUE, ColorUtils::DARK_BLUE, ColorUtils::BLUE,
                    ColorUtils::DARK_PURPLE, ColorUtils::DARK_PURPLE, ColorUtils::LIGHT_PURPLE
                ]);
            case self::ATOMIC:
                return new self($id, "Atomic", [
                    ColorUtils::RED, ColorUtils::RED, ColorUtils::GREEN,
                    ColorUtils::GREEN, ColorUtils::DARK_AQUA, ColorUtils::BLUE, ColorUtils::BLUE
                ]);
            case self::SUNSET:
                return new self($id, "Sunset", [
                    ColorUtils::DARK_PURPLE, ColorUtils::DARK_PURPLE, ColorUtils::RED,
                    ColorUtils::RED, ColorUtils::GOLD, ColorUtils::GOLD, ColorUtils::YELLOW
                ]);
            case self::TIME:
                return new self($id, "Time", [
                    ColorUtils::YELLOW, ColorUtils::YELLOW, ColorUtils::GOLD,
                    ColorUtils::RED, ColorUtils::LIGHT_PURPLE, ColorUtils::LIGHT_PURPLE, ColorUtils::DARK_PURPLE
                ]);
            case self::WINTER:
                return new self($id, "Winter", [
                    ColorUtils::DARK_BLUE, ColorUtils::BLUE, ColorUtils::DARK_AQUA,
                    ColorUtils::AQUA, ColorUtils::WHITE, ColorUtils::GRAY, ColorUtils::GRAY
                ]);
            case self::OBSIDIAN:
                return new self($id, "Obsidian", [
                    ColorUtils::BLACK, ColorUtils::DARK_PURPLE, ColorUtils::DARK_GRAY,
                    ColorUtils::DARK_GRAY, ColorUtils::DARK_PURPLE, ColorUtils::DARK_PURPLE, ColorUtils::BLACK
                ]);
            case self::SPRING:
                return new self($id, "Spring", [
                    ColorUtils::DARK_GREEN, ColorUtils::DARK_GREEN, ColorUtils::GREEN,
                    ColorUtils::YELLOW, ColorUtils::GOLD, ColorUtils::DARK_PURPLE, ColorUtils::LIGHT_PURPLE
                ]);
            case self::ICE:
                return new self($id, "Ice", [
                    ColorUtils::WHITE, ColorUtils::WHITE, ColorUtils::AQUA,
                    ColorUtils::AQUA, ColorUtils::DARK_AQUA, ColorUtils::DARK_AQUA, ColorUtils::DARK_AQUA
                ]);
            case self::SUMMER:
                return new self($id, "Summer", [
                    ColorUtils::DARK_AQUA, ColorUtils::AQUA, ColorUtils::YELLOW,
                    ColorUtils::YELLOW, ColorUtils::GOLD, ColorUtils::LIGHT_PURPLE, ColorUtils::DARK_PURPLE
                ]);
            case self::SPINEL:
                return new self($id, "Spinel", [
                    ColorUtils::WHITE, ColorUtils::DARK_RED, ColorUtils::RED,
                    ColorUtils::RED, ColorUtils::BLUE, ColorUtils::DARK_BLUE, ColorUtils::BLUE
                ]);
            case self::AUTUMN:
                return new self($id, "Autumn", [
                    ColorUtils::DARK_PURPLE, ColorUtils::DARK_PURPLE, ColorUtils::RED,
                    ColorUtils::GOLD, ColorUtils::YELLOW, ColorUtils::AQUA, ColorUtils::DARK_AQUA
                ]);
            case self::MYSTIC:
                return new self($id, "Mystic", [
                    ColorUtils::DARK_GREEN, ColorUtils::GREEN, ColorUtils::WHITE,
                    ColorUtils::WHITE, ColorUtils::GREEN, ColorUtils::GREEN, ColorUtils::DARK_GREEN
                ]);
            case self::ETERNAL:
                return new self($id, "Eternal", [
                    ColorUtils::DARK_RED, ColorUtils::DARK_RED, ColorUtils::DARK_PURPLE,
                    ColorUtils::BLUE, ColorUtils::BLUE, ColorUtils::DARK_BLUE, ColorUtils::BLACK
                ]);
            default:
                return null;
        }
    }

    public function getStarForPrestige(): string {
        switch ($this->ordinal) {
            case self::NONE:
            case self::IRON:
            case self::GOLD:
            case self::DIAMOND:
            case self::EMERALD:
            case self::SAPPHIRE:
            case self::RUBY:
            case self::CRYSTAL:
            case self::OPAL:
            case self::AMETHYST:
            case self::RAINBOW:
                return '✫';

            case self::IRON_PRIME:
            case self::GOLD_PRIME:
            case self::DIAMOND_PRIME:
            case self::EMERALD_PRIME:
            case self::SAPPHIRE_PRIME:
            case self::RUBY_PRIME:
            case self::CRYSTAL_PRIME:
            case self::OPAL_PRIME:
            case self::AMETHYST_PRIME:
            case self::MIRROR:
                return '✪';

            case self::LIGHT:
            case self::DAWN:
            case self::DUSK:
            case self::AIR:
            case self::WIND:
            case self::NEBULA:
            case self::THUNDER:
            case self::EARTH:
            case self::WATER:
            case self::FIRE:
                return '⚝';

            case self::SUNRISE:
            case self::ECLIPSE:
            case self::GAMMA:
            case self::MAJESTIC:
            case self::ANDESINE:
            case self::MARINE:
            case self::ELEMENT:
            case self::GALAXY:
            case self::ATOMIC:
            case self::SUNSET:
            case self::TIME:
            case self::WINTER:
            case self::OBSIDIAN:
            case self::SPRING:
            case self::ICE:
            case self::SUMMER:
            case self::SPINEL:
            case self::AUTUMN:
            case self::MYSTIC:
            case self::ETERNAL:
                return '✥';

            default:
                return '?';
        }
    }

    /**
     * @return mixed
     */
    public function getOrdinal() {
        return $this->ordinal;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getColors() {
        return $this->colors;
    }
}