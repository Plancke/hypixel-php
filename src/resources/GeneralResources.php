<?php

namespace Plancke\HypixelPHP\resources;

class GeneralResources extends AResources {

    public function getAchievements() {
        return AResources::requireResourceFile('Achievements.php');
    }

}