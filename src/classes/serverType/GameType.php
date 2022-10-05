<?php

namespace Plancke\HypixelPHP\classes\serverType;

/**
 * Class GameType
 * @package Plancke\HypixelPHP\classes\serverType
 */
class GameType extends ServerType {

    protected $boosters;

    /**
     * @param string $enum
     * @param string $db
     * @param string $name
     * @param string $short
     * @param int $id
     * @param bool $boosters
     */
    public function __construct($enum, $db, $name, $short, $id, $boosters = true) {
        parent::__construct($enum, $db, $name, $short, $id);
        $this->boosters = $boosters;
    }

    /**
     * @return boolean
     */
    public function hasBoosters() {
        return $this->boosters;
    }

}