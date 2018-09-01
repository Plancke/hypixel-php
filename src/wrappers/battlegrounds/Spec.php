<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds;

class Spec {
    protected $name, $id;

    /**
     * Spec constructor.
     * @param string $name
     * @param int $id
     */
    function __construct($name, $id) {
        $this->name = $name;
        $this->id = $id;
    }

    /**
     * @return string
     */
    function getName() {
        return $this->name;
    }

    /**
     * @return int
     */
    function getID() {
        return $this->id;
    }
}