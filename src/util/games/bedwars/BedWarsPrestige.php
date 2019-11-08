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

    const HIGHEST_PRESTIGE = self::RAINBOW;

    private static $cache = [];
    private static $rainbowColors = [ColorUtils::RED, ColorUtils::GOLD, ColorUtils::YELLOW, ColorUtils::GREEN, ColorUtils::AQUA, ColorUtils::LIGHT_PURPLE, ColorUtils::DARK_PURPLE];

    protected $ordinal;
    protected $name;
    protected $color;

    /**
     * BedWarsPrestige constructor.
     * @param $ordinal
     * @param $name
     * @param $color
     */
    public function __construct($ordinal, $name, $color) {
        $this->ordinal = $ordinal;
        $this->name = $name;
        $this->color = $color;
    }

    /**
     * @return array
     */
    public static function values() {
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
            self::RAINBOW
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
            case BedWarsPrestige::NONE:
                return new BedWarsPrestige(BedWarsPrestige::NONE, "None", ColorUtils::GRAY);
            case BedWarsPrestige::IRON:
                return new BedWarsPrestige(BedWarsPrestige::IRON, "IRon", ColorUtils::WHITE);
            case BedWarsPrestige::GOLD:
                return new BedWarsPrestige(BedWarsPrestige::GOLD, "Gold", ColorUtils::GOLD);
            case BedWarsPrestige::DIAMOND:
                return new BedWarsPrestige(BedWarsPrestige::DIAMOND, "Diamond", ColorUtils::AQUA);
            case BedWarsPrestige::EMERALD:
                return new BedWarsPrestige(BedWarsPrestige::EMERALD, "Emerald", ColorUtils::DARK_GREEN);
            case BedWarsPrestige::SAPPHIRE:
                return new BedWarsPrestige(BedWarsPrestige::SAPPHIRE, "Sapphire", ColorUtils::DARK_AQUA);
            case BedWarsPrestige::RUBY:
                return new BedWarsPrestige(BedWarsPrestige::RUBY, "Ruby", ColorUtils::DARK_RED);
            case BedWarsPrestige::CRYSTAL:
                return new BedWarsPrestige(BedWarsPrestige::CRYSTAL, "Crystal", ColorUtils::LIGHT_PURPLE);
            case BedWarsPrestige::OPAL:
                return new BedWarsPrestige(BedWarsPrestige::OPAL, "Opal", ColorUtils::BLUE);
            case BedWarsPrestige::AMETHYST:
                return new BedWarsPrestige(BedWarsPrestige::AMETHYST, "Amethyst", ColorUtils::DARK_PURPLE);
            case BedWarsPrestige::RAINBOW:
                return new BedWarsPrestige(BedWarsPrestige::RAINBOW, "Rainbow", ColorUtils::WHITE);
            default:
                return null;
        }
    }

    /**
     * @return array
     */
    public static function getRainbowColors(): array {
        return self::$rainbowColors;
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
     * @return string
     */
    public function getColor() {
        return $this->color;
    }
}