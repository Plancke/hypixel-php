<?php

namespace Plancke\HypixelPHP\resources;

use Plancke\HypixelPHP\responses\Resource;

/**
 * Class GeneralResources
 * @package Plancke\HypixelPHP\resources
 */
class GeneralResources extends Resources {
    /**
     * @return Resource
     */
    public function getAchievements() {
        return self::requireRemoteResourceFile('achievements');
    }

    /**
     * @return Resource
     */
    public function getQuests() {
        return self::requireRemoteResourceFile('quests');
    }

    /**
     * @return Resource
     */
    public function getChallenges() {
        return self::requireRemoteResourceFile('challenges');
    }

}