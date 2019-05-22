<?php

namespace Plancke\Tests;

use PHPUnit\Framework\TestCase;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\responses\PlayerCount;
use Plancke\Tests\util\TestUtil;

class ResponseTest extends TestCase {

    /**
     * @throws HypixelPHPException
     */
    function testPlayerCount() {
        // basic check to confirm stuff is getting mapped correctly
        // TODO check api up status or this test will fail
        $playerCount = TestUtil::getHypixelPHP()->getPlayerCount();
        $this->assertTrue($playerCount instanceof PlayerCount);
    }

}