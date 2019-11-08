<?php

namespace Plancke\HypixelPHP\util\games;

use Plancke\HypixelPHP\responses\player\GameStats;

/**
 * Class BlitzUtils
 * @package Plancke\HypixelPHP\util\games
 */
class BlitzUtils {

    /**
     * @param $resources
     * @param GameStats $stats
     * @param $kit
     * @return int
     */
    public function getKitLevel($resources, GameStats $stats, $kit) {
        $purchased_level = $stats->getNumber($kit);
        if ($purchased_level > 0) return $purchased_level;

        $exp = $stats->getNumber('exp_' . $kit);
        if ($exp > 0) {
            $last = 0;
            foreach ($resources['kits']['levels'] as $ordinal => $level) {
                if ($exp < $level['exp']) {
                    break;
                }
                $last = $ordinal;
            }
            return $last;
        }

        return 0;
    }

}