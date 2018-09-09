<?php

namespace Plancke\HypixelPHP\resources;

/**
 * Class GuildResources
 * @package Plancke\HypixelPHP\resources
 */
class GuildResources extends Resources {
    /**
     * @return array
     */
    public function getAchievements() {
        return Resources::requireResourceFile('guild/Achievements.php');
    }

    /**
     * @return array
     */
    public function getRankWhitelist() {
        return Resources::requireResourceFile('guild/RankWhitelist.php');
    }

}