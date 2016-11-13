<?php

namespace Plancke\HypixelPHP\util\resources;

class GeneralResources extends AResources {

    public function getAchievements() {
        return AResources::requireResourceFile('Achievements.php');
    }

}