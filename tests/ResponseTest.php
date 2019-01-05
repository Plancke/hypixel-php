<?php

namespace Plancke\Tests;

use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\player\Stats;
use Plancke\HypixelPHP\responses\PlayerCount;
use Plancke\Tests\util\TestUtil;

class ResponseTest extends \PHPUnit_Framework_TestCase {

    private $enabled = false;

    /**
     * @throws \Plancke\HypixelPHP\exceptions\HypixelPHPException
     */
    function testPlayerResponse() {
        if (!$this->enabled) return;
        $player = TestUtil::getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => TestUtil::PLANCKE]);
        $this->assertTrue($player instanceof Player);
        $this->assertTrue($player->getStats() instanceof Stats);
    }

    /**
     * @throws \Plancke\HypixelPHP\exceptions\HypixelPHPException
     */
    function testPlayerCount() {
        if (!$this->enabled) return;
        $playerCount = TestUtil::getHypixelPHP()->getPlayerCount();
        $this->assertTrue($playerCount instanceof PlayerCount);
    }

}