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

}