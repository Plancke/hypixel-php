<?php

namespace Plancke\HypixelPHP\util\resources;

class GameResources extends AResources {

    public static function getTNTWizards() {
        return AResources::requireResourceFile('game_info/tntgames/Wizards.php');
    }

    public static function getArena() {
        return AResources::requireResourceFile('game_info/Arena.php');
    }

    public static function getBattlegrounds() {
        return AResources::requireResourceFile('game_info/Battlegrounds.php');
    }

    public static function getHungerGames() {
        return AResources::requireResourceFile('game_info/HungerGames.php');
    }

    public static function getPaintball() {
        return AResources::requireResourceFile('game_info/Paintball.php');
    }

    public static function getSkyClash() {
        return AResources::requireResourceFile('game_info/SkyClash.php');
    }

    public static function getSuperSmash() {
        return AResources::requireResourceFile('game_info/SuperSmash.php');
    }

    public static function getWallsThree() {
        return AResources::requireResourceFile('game_info/Walls3.php');
    }

    public static function getAchievements() {
        return AResources::requireResourceFile('Achievements.php');
    }

}