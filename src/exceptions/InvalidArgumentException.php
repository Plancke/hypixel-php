<?php

namespace Plancke\HypixelPHP\exceptions;

/**
 * Class InvalidArgumentException
 * @package Plancke\HypixelPHP\exceptions
 */
class InvalidArgumentException extends HypixelPHPException {

    public function __construct() {
        parent::__construct("Invalid Argument", ExceptionCodes::INVALID_ARGUMENT);
    }

}