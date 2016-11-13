<?php

namespace Plancke\HypixelPHP\util;

use Plancke\HypixelPHP\util\resources\AResources;

class GeneralResources extends AResources {

    public static function getAchievements() {
        return AResources::requireResourceFile('Achievements.php');
    }

}