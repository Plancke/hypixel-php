<?php

namespace Plancke\HypixelPHP\exceptions;

/**
 * Class BadResponseCodeException
 * @package Plancke\HypixelPHP\exceptions
 */
class BadResponseCodeException extends HypixelPHPException {

    protected $expectedCode, $actualCode;

    /**
     * BadResponseCodeException constructor.
     * @param int $expectedCode
     * @param int $actualCode
     */
    public function __construct($expectedCode, $actualCode) {
        parent::__construct("Bad Response Code ($expectedCode/$actualCode)", ExceptionCodes::BAD_RESPONSE_CODE);

        $this->expectedCode = $expectedCode;
        $this->actualCode = $actualCode;
    }

    /**
     * @return int
     */
    public function getExpected() {
        return $this->expectedCode;
    }

    /**
     * @return int
     */
    public function getActualCode() {
        return $this->actualCode;
    }

}