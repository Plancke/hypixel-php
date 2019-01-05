<?php

namespace Plancke\HypixelPHP\resources;

/**
 * Class GameResources
 * @package Plancke\HypixelPHP\resources
 */
class GameResources extends Resources {

    /**
     * @return array
     */
    public function getTNTWizards() {
        return Resources::requireResourceFile('game_info/tntgames/Wizards.php');
    }

    /**
     * @return array
     */
    public function getArena() {
        return Resources::requireResourceFile('game_info/Arena.php');
    }

    /**
     * @return array
     */
    public function getBattlegrounds() {
        return Resources::requireResourceFile('game_info/Battlegrounds.php');
    }

    /**
     * @return array
     * @deprecated
     */
    public function getHungerGames() {
        return Resources::requireResourceFile('game_info/SurvivalGames.php');
    }

    /**
     * @return array
     */
    public function getSurvivalGames() {
        return Resources::requireResourceFile('game_info/SurvivalGames.php');
    }

    /**
     * @return array
     */
    public function getPaintball() {
        return Resources::requireResourceFile('game_info/Paintball.php');
    }

    /**
     * @return array
     */
    public function getSkyClash() {
        return Resources::requireResourceFile('game_info/SkyClash.php');
    }

    /**
     * @return array
     */
    public function getSuperSmash() {
        return Resources::requireResourceFile('game_info/SuperSmash.php');
    }

    /**
     * @return array
     */
    public function getWallsThree() {
        return Resources::requireResourceFile('game_info/Walls3.php');
    }

    /**
     * @return array
     */
    public function getSkyWars() {
        return Resources::requireResourceFile('game_info/SkyWars.php');
    }

}