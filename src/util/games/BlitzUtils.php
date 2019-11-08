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

        return $stats->getNumber($kit);
    }

}