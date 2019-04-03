<?php

namespace Plancke\Tests;

use PHPUnit\Framework\TestCase;
use Plancke\HypixelPHP\util\games\GameUtils;

class LevelTest extends TestCase {

    function testLevels() {
        $this->_testLevel(0, 0, 0);

        $this->_testLevel(708325, 146, 1);
        $this->_testLevel(199146, 42, 0);
        $this->_testLevel(1174460, 242, 2);
    }

    /**
     * @param $exp
     * @param $expectedLevel
     * @param $expectedPrestigeOrdinal
     */
    function _testLevel($exp, $expectedLevel, $expectedPrestigeOrdinal) {
        $level = GameUtils::getBedWars()->getExpCalculator()->getLevelForExp($exp);
        $this->assertEquals($expectedLevel, $level);
        $prestige = GameUtils::getBedWars()->getExpCalculator()->getPrestigeForLevel($level);
        $this->assertEquals($expectedPrestigeOrdinal, $prestige->getOrdinal());
    }
}