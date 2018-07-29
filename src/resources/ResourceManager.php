<?php

namespace Plancke\HypixelPHP\resources;

use Plancke\HypixelPHP\classes\Module;

/**
 * Class ResourceManager
 * @package Plancke\HypixelPHP\resources
 */
class ResourceManager extends Module {

    protected $gameResources;
    protected $generalResources;
    protected $guildResources;

    /**
     * @return GameResources
     */
    public function getGameResources() {
        if ($this->gameResources == null) {
            $this->gameResources = new GameResources();
        }
        return $this->gameResources;
    }

    /**
     * @param GameResources $gameResources
     * @return $this
     */
    public function setGameResources(GameResources $gameResources) {
        $this->gameResources = $gameResources;
        return $this;
    }

    /**
     * @return GeneralResources
     */
    public function getGeneralResources() {
        if ($this->generalResources == null) {
            $this->generalResources = new GeneralResources();
        }
        return $this->generalResources;
    }

    /**
     * @param GeneralResources $generalResources
     * @return $this
     */
    public function setGeneralResources(GeneralResources $generalResources) {
        $this->generalResources = $generalResources;
        return $this;
    }

    /**
     * @return GuildResources
     */
    public function getGuildResources() {
        if ($this->guildResources == null) {
            $this->guildResources = new GuildResources();
        }
        return $this->guildResources;
    }

    /**
     * @param GuildResources $guildResources
     * @return $this
     */
    public function setGuildResources(GuildResources $guildResources) {
        $this->guildResources = $guildResources;
        return $this;
    }


}