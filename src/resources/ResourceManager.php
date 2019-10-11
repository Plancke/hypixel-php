<?php

namespace Plancke\HypixelPHP\resources;

use Plancke\HypixelPHP\classes\Module;
use Plancke\HypixelPHP\HypixelPHP;

/**
 * Class ResourceManager
 * @package Plancke\HypixelPHP\resources
 */
class ResourceManager extends Module {

    protected $gameResources;
    protected $generalResources;
    protected $guildResources;

    /**
     * ResourceManager constructor.
     * @param HypixelPHP $HypixelPHP
     */
    function __construct(HypixelPHP $HypixelPHP) {
        parent::__construct($HypixelPHP);

        $this->gameResources = new GameResources($this);
        $this->generalResources = new GeneralResources($this);
        $this->guildResources = new GuildResources($this);
    }

    /**
     * @return GameResources
     */
    public function getGameResources() {
        return $this->gameResources;
    }

    /**
     * @return GeneralResources
     */
    public function getGeneralResources() {
        return $this->generalResources;
    }

    /**
     * @return GuildResources
     */
    public function getGuildResources() {
        return $this->guildResources;
    }

}