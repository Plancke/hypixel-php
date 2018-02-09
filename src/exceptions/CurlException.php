<?php

namespace Plancke\HypixelPHP\exceptions;

/**
 * Class CurlException
 * @package Plancke\HypixelPHP\exceptions
 */
class CurlException extends HypixelPHPException {

    protected $error;

    /**
     * CurlException constructor.
     * @param string $error
     */
    public function __construct($error) {
        parent::__construct("Failed to execute curl.", ExceptionCodes::CURL);

        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getError() {
        return $this->error;
    }

}