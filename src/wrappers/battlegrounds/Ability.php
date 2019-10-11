<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds;

class Ability {
    protected $name, $type, $spec;

    /**
     * Ability constructor.
     * @param string $name
     * @param string $spec
     * @param string $type
     */
    function __construct($name, $spec, $type) {
        $this->name = $name;
        $this->spec = $spec;
        $this->type = $type;
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
    function getSpec() {
        return $this->spec;
    }

    /**
     * @return string
     */
    function getType() {
        return $this->type;
    }
}