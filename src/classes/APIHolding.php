<?php

namespace Plancke\HypixelPHP\classes;

use Plancke\HypixelPHP\HypixelPHP;

abstract class APIHolding {

    private $HypixelPHP;

    /**
     * @param HypixelPHP $HypixelPHP
     */
    public function __construct(HypixelPHP $HypixelPHP) {
        $this->HypixelPHP = $HypixelPHP;
    }

    /**
     * @return HypixelPHP
     */
    public function getHypixelPHP() {
        return $this->HypixelPHP;
    }

    /**
     * @param $HypixelPHP
     * @return $this
     */
    public function setHypixelPHP($HypixelPHP) {
        $this->HypixelPHP = $HypixelPHP;
        return $this;
    }

}