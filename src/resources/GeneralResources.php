<?php

namespace Plancke\HypixelPHP\resources;

class GeneralResources extends AResources {
    /**
     * @return array
     */
    public function getAchievements() {
        return AResources::requireResourceFile('Achievements.php');
    }

}