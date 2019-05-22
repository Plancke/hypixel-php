<?php

namespace Plancke\Tests;

use PHPUnit\Framework\TestCase;
use Plancke\HypixelPHP\util\Validator;

class ValidatorTest extends TestCase {

    function testUUID() {
        $this->assertTrue(Validator::isAnyUUID('f025c1c7-f55a-4ea0-b8d9-3f47d17dfe0f')); // dashes
        $this->assertTrue(Validator::isAnyUUID('f025c1c7f55a4ea0b8d93f47d17dfe0f')); // no dashes

        $this->assertFalse(Validator::isAnyUUID('f025c1c7f55a9ea0b8d93f47d17dfe0f')); // bad version
        $this->assertFalse(Validator::isAnyUUID('f025c1c7f55a4ea0b8d93f47d17dfe0g')); // bad last character
        $this->assertFalse(Validator::isAnyUUID('f025c1c7f55a4ea0b8d93f47d17dfe0')); // too short
        $this->assertFalse(Validator::isAnyUUID('f025c1c7-f55a-4ea0-b8d9-3f47d17dfe0ff')); // too long
    }

    function testUsername() {
        $this->assertTrue(Validator::isUsername('Plancke'));
        $this->assertTrue(Validator::isUsername('Plancke_R')); // underscore
        $this->assertTrue(Validator::isUsername('G')); // 1 letter og

        $this->assertFalse(Validator::isUsername('Plancke-R'));// dash
        $this->assertFalse(Validator::isUsername('hello world')); // spaces
    }

    /**
     * @depends testUUID
     */
    function testAPIKey() {
        // any uuid will do but just double checking separately in case anything changes down the line
        $this->assertTrue(Validator::isValidAPIKey('f025c1c7-f55a-4ea0-b8d9-3f47d17dfe0f'));
        $this->assertTrue(Validator::isValidAPIKey('f025c1c7f55a4ea0b8d93f47d17dfe0f'));

        $this->assertFalse(Validator::isValidAPIKey('Plancke'));
    }
}