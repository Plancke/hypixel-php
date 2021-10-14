<?php

namespace Plancke\Tests;

use PHPUnit\Framework\TestCase;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\responses\counts\Counts;
use Plancke\Tests\util\TestUtil;

class ResponseTest extends TestCase {

    /**
     * @throws HypixelPHPException
     */
    function testPlayerCount() {
        // basic check to confirm stuff is getting mapped correctly
        // TODO check api up status or this test will fail
        $counts = TestUtil::getHypixelPHP()->getCounts();
        $this->assertTrue($counts instanceof Counts);
    }

}