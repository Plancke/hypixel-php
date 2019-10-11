<?php

namespace Plancke\HypixelPHP\resources;

use Plancke\HypixelPHP\resources\games\SkyBlockResources;
use Plancke\HypixelPHP\responses\Resource;

/**
 * Class GameResources
 * @package Plancke\HypixelPHP\resources
 */
class GameResources extends Resources {

    protected $skyblock;

    /**
     * GameResources constructor.
     * @param ResourceManager $resourceManager
     */
    public function __construct(ResourceManager $resourceManager) {
        parent::__construct($resourceManager);

        $this->skyblock = new SkyBlockResources($this->resourceManager);
    }

    /**
     * @return Resource
     */
    public function getTNTWizards() {
        return $this->requireResourceFile('game_info/tntgames/Wizards.php');
    }

    /**
     * @return Resource
     */
    public function getArena() {
        return $this->requireResourceFile('game_info/Arena.php');
    }

    /**
     * @return Resource
     */
    public function getBattlegrounds() {
        return $this->requireResourceFile('game_info/Battlegrounds.php');
    }

    /**
     * @return Resource
     */
    public function getSurvivalGames() {
        return $this->requireResourceFile('game_info/SurvivalGames.php');
    }

    /**
     * @return Resource
     */
    public function getPaintball() {
        return $this->requireResourceFile('game_info/Paintball.php');
    }

    /**
     * @return Resource
     */
    public function getSkyClash() {
        return $this->requireResourceFile('game_info/SkyClash.php');
    }

    /**
     * @return Resource
     */
    public function getSuperSmash() {
        return $this->requireResourceFile('game_info/SuperSmash.php');
    }

    /**
     * @return Resource
     */
    public function getWallsThree() {
        return $this->requireResourceFile('game_info/Walls3.php');
    }

    /**
     * @return Resource
     */
    public function getSkyWars() {
        return $this->requireResourceFile('game_info/SkyWars.php');
    }

    /**
     * @return SkyBlockResources
     */
    public function getSkyBlock() {
        return $this->skyblock;
    }
}