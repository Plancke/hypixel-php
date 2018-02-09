<?php

namespace Plancke\HypixelPHP\color;

/**
 * Class ColorUtils
 * @package Plancke\HypixelPHP\color
 */
class ColorUtils {

    const COLOR_CHAR = '§';

    // colors
    const BLACK = '§0';
    const DARK_BLUE = '§1';
    const DARK_GREEN = '§2';
    const DARK_AQUA = '§3';
    const DARK_RED = '§4';
    const DARK_PURPLE = '§5';
    const GOLD = '§6';
    const GRAY = '§7';
    const DARK_GRAY = '§8';
    const BLUE = '§9';
    const GREEN = '§a';
    const AQUA = '§b';
    const RED = '§c';
    const LIGHT_PURPLE = '§d';
    const YELLOW = '§e';
    const WHITE = '§f';
    // formatting
    const BOLD = '§l';
    const STRIKETHROUGH = '§m';
    const UNDERLINE = '§n';
    const ITALIC = '§o';
    // special
    const RESET = '§r';
    const MAGIC = '§k';

    const NAME_TO_CODE = [
        "BLACK" => self::BLACK,
        "DARK_BLUE" => self::DARK_BLUE,
        "DARK_GREEN" => self::DARK_GREEN,
        "DARK_AQUA" => self::DARK_AQUA,
        "DARK_RED" => self::DARK_RED,
        "DARK_PURPLE" => self::DARK_PURPLE,
        "GOLD" => self::GOLD,
        "GRAY" => self::GRAY,
        "DARK_GRAY" => self::DARK_GRAY,
        "BLUE" => self::BLUE,
        "GREEN" => self::GREEN,
        "AQUA" => self::AQUA,
        "RED" => self::RED,
        "LIGHT_PURPLE" => self::LIGHT_PURPLE,
        "YELLOW" => self::YELLOW,
        "WHITE" => self::WHITE,
        "MAGIC" => self::MAGIC,
        "BOLD" => self::BOLD,
        "STRIKETHROUGH" => self::STRIKETHROUGH,
        "UNDERLINE" => self::UNDERLINE,
        "ITALIC" => self::ITALIC,
        "RESET" => self::RESET,
    ];
    const STRIP_COLOR_REGEX = '/§[0-9a-flmnokr]/i';
    protected static $COLOR_PARSER;

    //<editor-fold desc="stripping color">

    /**
     * Return an array of all color codes
     *
     * @return array
     */
    public static function getAllColors() {
        return [
            self::BLACK,
            self::DARK_BLUE,
            self::DARK_GREEN,
            self::DARK_AQUA,
            self::DARK_RED,
            self::DARK_PURPLE,
            self::GOLD,
            self::GRAY,
            self::DARK_GRAY,
            self::BLUE,
            self::GREEN,
            self::AQUA,
            self::RED,
            self::LIGHT_PURPLE,
            self::YELLOW,
            self::WHITE
        ];
    }

    /**
     * Return an array of all formatting codes
     *
     * @return array
     */
    public static function getAllFormattingCodes() {
        return [
            self::BOLD,
            self::STRIKETHROUGH,
            self::UNDERLINE,
            self::ITALIC
        ];
    }
    //</editor-fold>


    //<editor-fold desc="color handling">

    /**
     * Removes all MC encoded colors from a string
     * @param $string
     * @return string
     */
    public static function stripColors($string) {
        if ($string == null) {
            return null;
        }

        return preg_replace(self::STRIP_COLOR_REGEX, '', $string);
    }

    /**
     * @return ColorParser
     */
    public static function getColorParser() {
        if (self::$COLOR_PARSER == null) {
            self::$COLOR_PARSER = new ColorParser();
        }
        return self::$COLOR_PARSER;
    }

    /**
     * @param ColorParser $colorParser
     */
    public static function setColorParser(ColorParser $colorParser) {
        self::$COLOR_PARSER = $colorParser;
    }
    //</editor-fold>

}