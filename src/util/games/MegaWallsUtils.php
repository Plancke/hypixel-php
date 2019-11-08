<?php

namespace Plancke\HypixelPHP\util\games;

use Plancke\HypixelPHP\responses\player\GameStats;

/**
 * Class BedWarsUtils
 * @package Plancke\HypixelPHP\util\games
 */
class MegaWallsUtils {

    /**
     * @param GameStats $stats
     * @param array $class
     * @param array $skill
     * @return int
     */
    public function getSkillLevel($stats, $class, $skill) {
        $minLevel = 1;
        if (array_key_exists('starter', $class) && $class['starter']) {
            if (array_key_exists('maxLevel', $skill)) {
                if ($skill['maxLevel'] == 5) {
                    $minLevel = 5;
                } else {
                    $minLevel = 2;
                }
            }
        }

        $level = $stats->getNumber(str_replace("%class%", strtolower($class['id']), $skill['field']), 1);
        return max($minLevel, $level);
    }

    /**
     * @param GameStats $stats
     * @param array $class
     * @param array $field
     * @return int
     */
    public function getFieldLevel($stats, $class, $field) {
        return $stats->getNumber(str_replace("%class%", strtolower($class['id']), $field['field']), 0);
    }

}