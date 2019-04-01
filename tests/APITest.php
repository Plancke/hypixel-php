<?php

namespace Plancke\Tests;

use PHPUnit\Framework\TestCase;
use Plancke\HypixelPHP\exceptions\ExceptionCodes;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\HypixelPHP;

class APITest extends TestCase {

    function testNoKey() {
        try {
            new HypixelPHP(null);
        } catch (HypixelPHPException $e) {
            $this->assertEquals(ExceptionCodes::NO_KEY, $e->getCode());
        }
    }

    function testInvalidKey() {
        try {
            new HypixelPHP("INVALID");
        } catch (HypixelPHPException $e) {
            $this->assertEquals(ExceptionCodes::INVALID_KEY, $e->getCode());
        }
    }

    function testValidKey() {
        try {
            new HypixelPHP("b13e2f50-a16c-4aa5-92a6-75e9b699b9fc");
        } catch (HypixelPHPException $e) {
            $this->fail();
        }

        $this->assertTrue(TRUE);
    }

}
