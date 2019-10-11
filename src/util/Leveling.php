<?php

namespace Plancke\HypixelPHP\util;

use Plancke\HypixelPHP\responses\player\Player;

/**
 * Class Leveling
 * @package Plancke\HypixelPHP\util
 */
class Leveling {

    const EXP_FIELD = "networkExp";
    const LVL_FIELD = "networkLevel";

    const BASE = 10000;
    const GROWTH = 2500;

    /* Constants to generate the total amount of XP to complete a level */
    const HALF_GROWTH = 0.5 * Leveling::GROWTH;

    /* Constants to look up the level from the total amount of XP */
    const REVERSE_PQ_PREFIX = -(Leveling::BASE - 0.5 * Leveling::GROWTH) / Leveling::GROWTH;
    const REVERSE_CONST = Leveling::REVERSE_PQ_PREFIX * Leveling::REVERSE_PQ_PREFIX;
    const GROWTH_DIVIDES_2 = 2 / Leveling::GROWTH;

    /**
     * This method returns the level of a player calculated by the current experience gathered. The result is
     * a precise level of the player The value is not zero-indexed and represents the visible level
     * for the player.
     * The result can't be smaller than 1 and negative experience results in level 1.
     * <p>
     * Examples:
     * -        0 XP = 1.0
     * -     5000 XP = 1.5
     * -    10000 XP = 2.0
     * -    50000 XP = 4.71...
     * - 79342431 XP = 249.46...
     *
     * @param float $exp Total experience gathered by the player.
     * @return float Exact level of player (Smallest value is 1.0)
     */
    static function getExactLevel(float $exp) {
        return Leveling::getLevel($exp) + Leveling::getPercentageToNextLevel($exp);
    }

    /**
     * This method returns the level of a player calculated by the current experience gathered. The result is
     * a precise level of the player The value is not zero-indexed and represents the absolute visible level
     * for the player.
     * The result can't be smaller than 1 and negative experience results in level 1.
     * <p>
     * Examples:
     * -        0 XP = 1.0
     * -     5000 XP = 1.0
     * -    10000 XP = 2.0
     * -    50000 XP = 4.0
     * - 79342431 XP = 249.0
     *
     * @param float $exp Absolute level of player (Smallest value is 1.0)
     * @return float Total exp experience gathered by the player.
     */
    static function getLevel(float $exp) {
        return $exp < 0 ? 1 : floor(1 + Leveling::REVERSE_PQ_PREFIX + sqrt(Leveling::REVERSE_CONST + Leveling::GROWTH_DIVIDES_2 * $exp));
    }

    /**
     * This method returns the current progress of this level to reach the next level. This method is as
     * precise as possible due to rounding errors on the mantissa. The first 10 decimals are totally
     * accurate.
     * <p>
     * Examples:
     * -     5000.0 XP   (Lv. 1) = 0.5                               (50 %)
     * -    22499.0 XP   (Lv. 2) = 0.99992                       (99.992 %)
     * -  5324224.0 XP  (Lv. 62) = 0.856763076923077   (85.6763076923077 %)
     * - 23422443.0 XP (Lv. 134) = 0.4304905109489051 (43.04905109489051 %)
     *
     * @param float $exp Current experience gathered by the player
     * @return float Current progress to the next level
     */
    static function getPercentageToNextLevel(float $exp) {
        $lv = Leveling::getLevel($exp);
        $x0 = Leveling::getTotalExpToLevel($lv);
        return ($exp - $x0) / (Leveling::getTotalExpToLevel($lv + 1) - $x0);
    }

    /**
     * This method returns the experience it needs to reach that level. If you want to reach the given level
     * you have to gather the amount of experience returned by this method. This method is precise, that means
     * you can pass any progress of a level to receive the experience to reach that progress. (5.764 to get
     * the experience to reach level 5 with 76.4% of level 6.
     * <p>
     * Examples:
     * -    1.0 =        0.0 XP
     * -    2.0 =    10000.0 XP
     * -    3.0 =    22500.0 XP
     * -    5.0 =    55000.0 XP
     * -  5.764 =    70280.0 XP
     * -  130.0 = 21930000.0 XP
     * - 250.43 = 79951975.0 XP
     *
     * @param float $level The level and progress of the level to reach
     * @return float The experience required to reach that level and progress
     */
    static function getTotalExpToLevel(float $level) {
        $lv = floor($level);
        $x0 = Leveling::getTotalExpToFullLevel($lv);
        if ($level == $lv) return $x0;
        return (Leveling:: getTotalExpToFullLevel($lv + 1) - $x0) * ($level % 1) + $x0;
    }

    /**
     * Helper method that may only be called by full levels and has the same functionality as getTotalExpToLevel()
     * but doesn't support progress and returns wrong values for progress due to perfect curve shape.
     *
     * @param float $level Level to receive the amount of experience to
     * @return float Experience to reach the given level
     */
    static function getTotalExpToFullLevel(float $level) {
        return (Leveling::HALF_GROWTH * ($level - 2) + Leveling::BASE) * ($level - 1);
    }

    /**
     * This method returns the amount of experience that is needed to progress from level to level + 1. (5 to 6)
     * The levels passed must absolute levels with the smallest level being 1. Smaller values always return
     * the BASE constant. The calculation is precise and if a decimal is passed it returns the XP from the
     * progress of this level to the next level with the same progress. (5.5 to 6.5)
     * <p>
     * Examples:
     * -   1 (to 2)   =  10000.0 XP
     * -   2 (to 3)   =  12500.0 XP
     * -   3 (to 4)   =  15000.0 XP
     * -   5 (to 6)   =  20000.0 XP
     * - 5.5 (to 6.5) =  21250.0 XP
     * - 130 (to 131) = 332500.0 XP
     * - 250 (to 251) = 632500.0 XP
     *
     * @param float $level Level from which you want to get the next level with the same level progress
     * @return float Experience to reach the next level with same progress
     */
    static function getExpFromLevelToNext(float $level) {
        return $level < 1 ? Leveling::BASE : Leveling::GROWTH * ($level - 1) + Leveling::BASE;
    }

    /**
     * @param Player $player
     * @return float
     */
    static function getExperience(Player $player) {
        $exp = $player->getNumber(Leveling::EXP_FIELD);
        $exp += Leveling::getTotalExpToFullLevel((float)$player->getNumber(Leveling::LVL_FIELD) + 1);
        return (float)$exp;
    }

}