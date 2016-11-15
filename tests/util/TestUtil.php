<?php

namespace Plancke\Tests\util;

use Plancke\HypixelPHP\HypixelPHP;

class TestUtil {

    static function getHypixelPHP() {
        $HypixelPHP = new HypixelPHP("");
        // log to error
        $HypixelPHP->setLogger(new CustomLogger($HypixelPHP));
        // only fetching
        $HypixelPHP->setCacheHandler(new NoCacheHandler($HypixelPHP));

        return $HypixelPHP;
    }

}