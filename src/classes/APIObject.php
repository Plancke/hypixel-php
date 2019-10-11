<?php

namespace Plancke\HypixelPHP\classes;

use Plancke\HypixelPHP\HypixelPHP;

/**
 * Class APIObject
 * @package Plancke\HypixelPHP\classes
 */
abstract class APIObject extends APIHolding {

    use DataHolding;

    protected $data;

    /**
     * APIObject constructor.
     * @param HypixelPHP $HypixelPHP
     * @param array $data
     */
    public function __construct(HypixelPHP $HypixelPHP, $data) {
        parent::__construct($HypixelPHP);

        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->getRaw();
    }

    /**
     * @return array
     */
    public function getRaw() {
        return $this->data;
    }
}