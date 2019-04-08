<?php

namespace Plancke\Tests;

use PHPUnit\Framework\TestCase;
use Plancke\HypixelPHP\util\Validator;

class ValidatorTest extends TestCase {

    function testUUIDMatcher() {
        $UUID = 'f025c1c7-f55a-4ea0-b8d9-3f47d17dfe0f'; // dashes
        $this->assertTrue(Validator::isAnyUUID($UUID));
        $UUID = 'f025c1c7f55a4ea0b8d93f47d17dfe0f'; // no dashes
        $this->assertTrue(Validator::isAnyUUID($UUID));
        $UUID = 'f025c1c7f55a9ea0b8d93f47d17dfe0f'; // bad version
        $this->assertFalse(Validator::isAnyUUID($UUID));
        $UUID = 'f025c1c7f55a4ea0b8d93f47d17dfe0g'; // bad last character
        $this->assertFalse(Validator::isAnyUUID($UUID));
        $UUID = 'f025c1c7f55a4ea0b8d93f47d17dfe0'; // too short
        $this->assertFalse(Validator::isAnyUUID($UUID));
        $UUID = 'f025c1c7-f55a-4ea0-b8d9-3f47d17dfe0ff'; // too long
        $this->assertFalse(Validator::isAnyUUID($UUID));
    }

    function testUsernameMatcher() {
        $name = 'Plancke';
        $this->assertTrue(Validator::isUsername($name));
        $name = 'Plancke-R'; // dash
        $this->assertFalse(Validator::isUsername($name));
        $name = 'Plancke_R'; // underscore
        $this->assertTrue(Validator::isUsername($name));
        $name = 'G'; // 1 letter og
        $this->assertTrue(Validator::isUsername($name));
        $name = 'hello world'; // spaces
        $this->assertFalse(Validator::isUsername($name));
    }
}