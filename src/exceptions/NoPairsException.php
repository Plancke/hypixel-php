<?php

namespace Plancke\HypixelPHP\exceptions;

class NoPairsException extends HypixelPHPException {

    public function __construct() {
        parent::__construct("No pairs given", ExceptionCodes::NO_PAIRS);
    }

}