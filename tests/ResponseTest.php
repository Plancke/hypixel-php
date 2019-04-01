<?php

namespace Plancke\Tests;

use PHPUnit\Framework\TestCase;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\player\Stats;
use Plancke\HypixelPHP\responses\PlayerCount;
use Plancke\Tests\util\TestUtil;

class ResponseTest extends TestCase {

    /**
     * @throws HypixelPHPException
     */
    function testPlayerResponse() {
        $player = TestUtil::getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => TestUtil::PLANCKE]);
        $this->assertTrue($player instanceof Player);
        $this->assertTrue($player->getStats() instanceof Stats);
    }

    /**
     * @throws HypixelPHPException
     */
    function testPlayerCount() {
        $playerCount = TestUtil::getHypixelPHP()->getPlayerCount();
        $this->assertTrue($playerCount instanceof PlayerCount);
    }

}