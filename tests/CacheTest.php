<?php

namespace Plancke\Tests;

use Plancke\HypixelPHP\util\CacheUtil;

class CacheTest extends \PHPUnit_Framework_TestCase {

    function testExpire() {
        $cachedTime = time() * 1000;

        // has it been expired for 5 seconds?
        $this->assertTrue(CacheUtil::isExpired($cachedTime, 0, 5000));

        // will it still be valid in 5 seconds?
        $this->assertFalse(CacheUtil::isExpired($cachedTime, 2000, -5000));

        $remain = CacheUtil::getRemainingTime($cachedTime, 0, [2000, 3000]);
        $this->assertTrue($remain >= 2000 && $remain <= 3000);
    }

}