<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds;

class PlayerClass {
    protected $name, $spec, $id;

    protected $specs = [
        PlayerClasses::MAGE => [0 => "Pyromancer", 1 => "Cryomancer", 2 => "Aquamancer"],
        PlayerClasses::WARRIOR => [0 => "Berserker", 1 => "Defender"],
        PlayerClasses::PALADIN => [0 => "Avenger", 1 => "Crusader", 2 => "Protector"],
        PlayerClasses::SHAMAN => [0 => "Thunderlord", 1 => "Earthwarden"],
    ];

    /**
     * PlayerClass constructor.
     * @param string $name
     * @param int $spec
     * @param int $id
     */
    function __construct($name, $spec, $id) {
        $this->name = $name;
        $this->spec = $spec;
        $this->id = $id;
    }

    /**
     * @return int
     */
    function getID() {
        return $this->id;
    }

    /**
     * @return string
     */
    function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    function getDisplay() {
        return ucfirst($this->name);
    }

    /**
     * @return Spec
     */
    function getSpec() {
        return new Spec($this->specs[$this->id][$this->spec], $this->spec);
    }
}