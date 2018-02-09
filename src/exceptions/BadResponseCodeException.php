<?php

namespace Plancke\HypixelPHP\exceptions;

/**
 * Class BadResponseCodeException
 * @package Plancke\HypixelPHP\exceptions
 */
class BadResponseCodeException extends HypixelPHPException {

    protected $expected, $actual;

    /**
     * BadResponseCodeException constructor.
     * @param int $expected
     * @param int $actual
     */
    public function __construct($expected, $actual) {
        parent::__construct("Bad Response Code ($expected/$actual)", ExceptionCodes::BAD_RESPONSE_CODE);

        $this->expected = $expected;
        $this->actual = $actual;
    }

    /**
     * @return int
     */
    public function getExpected() {
        return $this->expected;
    }

    /**
     * @return int
     */
    public function getActual() {
        return $this->actual;
    }

}