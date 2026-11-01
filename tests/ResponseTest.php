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
        if (TestUtil::getAPIKey() === null) {
            $this->markTestSkipped('Set API_KEY to run the live Hypixel API test.');
        }
        $counts = TestUtil::getHypixelPHP()->getCounts();
        $this->assertTrue($counts instanceof Counts);
    }

}