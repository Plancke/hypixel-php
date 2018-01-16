<?php

namespace Plancke\HypixelPHP\classes;

use Plancke\HypixelPHP\HypixelPHP;

/**
 * Class Module
 * @package Plancke\HypixelPHP\classes
 */
abstract class Module extends APIHolding {

    /**
     * Module constructor.
     * @param HypixelPHP $HypixelPHP
     */
    function __construct(HypixelPHP $HypixelPHP) {
        parent::__construct($HypixelPHP);
    }

}