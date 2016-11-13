<?php
namespace Plancke\Tests;

use Plancke\HypixelPHP\exceptions\ExceptionCodes;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\HypixelPHP;

class APITest extends \PHPUnit_Framework_TestCase {

    function testNoKey() {
        require_once 'bootstrap.php';

        try {
            new HypixelPHP(null);
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

    function testValidKey() {
        require_once 'bootstrap.php';

        new HypixelPHP("b13e2f50-a16c-4aa5-92a6-75e9b699b9fc");
    }
}
