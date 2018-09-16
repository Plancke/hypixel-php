<?php

namespace Plancke\HypixelPHP\resources;

/**
 * Class GeneralResources
 * @package Plancke\HypixelPHP\resources
 */
class GeneralResources extends Resources {
    /**
     * @return array
     */
    public function getAchievements() {
        return Resources::requireResourceFile('Achievements.php');
    }

    /**
     * @return array
     */
    public function getQuests() {
        return Resources::requireResourceFile('Quests.php');
    }

    /**
     * @return array
     */
    public function getChallenges() {
        return Resources::requireResourceFile('Challenges.php');
    }

}