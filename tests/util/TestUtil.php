<?php

namespace Plancke\Tests\util;

use Plancke\HypixelPHP\HypixelPHP;

class TestUtil {

    const PLANCKE = 'f025c1c7f55a4ea0b8d93f47d17dfe0f';

    static function getHypixelPHP() {
        $HypixelPHP = new HypixelPHP('');
        // log to error
        $HypixelPHP->setLogger(new CustomLogger($HypixelPHP));
        // only fetching
        $HypixelPHP->setCacheHandler(new NoCacheHandler($HypixelPHP));

        return $HypixelPHP;
    }

}