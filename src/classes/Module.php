<?php

namespace Plancke\HypixelPHP\classes;

use Plancke\HypixelPHP\HypixelPHP;

abstract class Module extends APIHolding {

    function __construct(HypixelPHP $HypixelPHP) {
        parent::__construct($HypixelPHP);
    }

}