<?php
namespace Plancke\Tests;

use Plancke\HypixelPHP\exceptions\ExceptionCodes;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\HypixelPHP;

class APITest extends \PHPUnit_Framework_TestCase {

    function testNoKey() {
        require_once 'bootstrap.php';

        try {
            new HypixelPHP("INVALID");
        } catch (HypixelPHPException $e) {
            $this->assertEquals(ExceptionCodes::NO_KEY, $e->getCode());
        }
    }

    function testInvalidKey() {
        require_once 'bootstrap.php';

        try {
            new HypixelPHP("INVALID");
        } catch (HypixelPHPException $e) {
            $this->assertEquals(ExceptionCodes::INVALID_KEY, $e->getCode());
        }
    }
}
