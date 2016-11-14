<?php

namespace Plancke\HypixelPHP\responses\player;

use Plancke\HypixelPHP\classes\APIObject;
use Plancke\HypixelPHP\classes\gameType\GameTypes;

class Stats extends APIObject {

    public function getGameFromID($id) {
        $gameType = GameTypes::fromID($id);
        if ($gameType != null) {
            return $this->getGame($gameType->getDb());
        }
        return null;
    }

    /**
     * @param $game
     *
     * @return GameStats
     */
    public function getGame($game) {
        $game = $this->getArray($game);
        return new GameStats($this->getHypixelPHP(), $game);
    }
}