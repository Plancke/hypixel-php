<?php

namespace Plancke\HypixelPHP\exceptions;

class InvalidUUIDException extends HypixelPHPException {

    public function __construct() {
        parent::__construct("Input isn't a valid UUID", ExceptionCodes::INVALID_UUID);
    }

}