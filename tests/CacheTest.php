<?php

namespace Plancke\Tests;

use Plancke\HypixelPHP\cache\impl\FlatFileCacheHandler;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\util\CacheUtil;
use Plancke\Tests\util\TestUtil;

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

    function testFlatFile() {
        $player = TestUtil::getHypixelPHP()
            ->setCacheHandler(new FlatFileCacheHandler(TestUtil::getHypixelPHP()))
            ->getPlayer([FetchParams::PLAYER_BY_NAME => "Plancke"]);
        $this->assertTrue($player instanceof Player);
        $this->assertTrue($player->getUUID() == TestUtil::PLANCKE);
    }
}