<?php

namespace Plancke\HypixelPHP\resources;

class GameResources extends AResources {

    /**
     * @return array
     */
    public function getTNTWizards() {
        return AResources::requireResourceFile('game_info/tntgames/Wizards.php');
    }

    /**
     * @return array
     */
    public function getArena() {
        return AResources::requireResourceFile('game_info/Arena.php');
    }

    /**
     * @return array
     */
    public function getBattlegrounds() {
        return AResources::requireResourceFile('game_info/Battlegrounds.php');
    }

    /**
     * @return array
     */
    public function getHungerGames() {
        return AResources::requireResourceFile('game_info/HungerGames.php');
    }

    /**
     * @return array
     */
    public function getPaintball() {
        return AResources::requireResourceFile('game_info/Paintball.php');
    }

    /**
     * @return array
     */
    public function getSkyClash() {
        return AResources::requireResourceFile('game_info/SkyClash.php');
    }

    /**
     * @return array
     */
    public function getSuperSmash() {
        return AResources::requireResourceFile('game_info/SuperSmash.php');
    }

    /**
     * @return array
     */
    public function getWallsThree() {
        return AResources::requireResourceFile('game_info/Walls3.php');
    }

}