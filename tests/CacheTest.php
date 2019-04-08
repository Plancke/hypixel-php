<?php

namespace Plancke\Tests;

use PHPUnit\Framework\TestCase;
use Plancke\HypixelPHP\util\CacheUtil;

class CacheTest extends TestCase {

    function getCurrentTimePlusOne() {
        return time() * 1000;
    }

    function testExpire() {
        // has it been expired for 5 seconds?
        $cachedTime = $this->getCurrentTimePlusOne();
        $this->assertTrue(CacheUtil::isExpired($cachedTime, 0, 5000));

        // will it still be valid in 5 seconds?
        $cachedTime = $this->getCurrentTimePlusOne();
        $this->assertFalse(CacheUtil::isExpired($cachedTime, 2000, -5000));

        $cachedTime = $this->getCurrentTimePlusOne();
        $remain = CacheUtil::getRemainingTime($cachedTime, 0, [2000, 3000]);
        $this->assertTrue($remain >= 2000 && $remain <= 3000);
    }

}