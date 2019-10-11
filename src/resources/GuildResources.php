<?php

namespace Plancke\HypixelPHP\resources;

use Plancke\HypixelPHP\responses\Resource;

/**
 * Class GuildResources
 * @package Plancke\HypixelPHP\resources
 */
class GuildResources extends Resources {
    /**
     * @return Resource
     */
    public function getAchievements() {
        return self::requireRemoteResourceFile('guild/achievements');
    }

    /**
     * @return Resource
     */
    public function getPermissions() {
        return self::requireRemoteResourceFile('guild/permissions');
    }

    /**
     * @return Resource
     */
    public function getRankWhitelist() {
        return self::requireResourceFile('guild/RankWhitelist.php');
    }

}