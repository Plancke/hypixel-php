<?php

namespace Plancke\HypixelPHP\util\resources;

class GameResources extends AResources {

    public function getTNTWizards() {
        return AResources::requireResourceFile('game_info/tntgames/Wizards.php');
    }

    public function getArena() {
        return AResources::requireResourceFile('game_info/Arena.php');
    }

    public function getBattlegrounds() {
        return AResources::requireResourceFile('game_info/Battlegrounds.php');
    }

    public function getHungerGames() {
        return AResources::requireResourceFile('game_info/HungerGames.php');
    }

    public function getPaintball() {
        return AResources::requireResourceFile('game_info/Paintball.php');
    }

    public function getSkyClash() {
        return AResources::requireResourceFile('game_info/SkyClash.php');
    }

    public function getSuperSmash() {
        return AResources::requireResourceFile('game_info/SuperSmash.php');
    }

    public function getWallsThree() {
        return AResources::requireResourceFile('game_info/Walls3.php');
    }

}