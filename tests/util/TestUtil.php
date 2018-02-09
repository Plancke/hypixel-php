<?php

namespace Plancke\Tests\util;

use Plancke\HypixelPHP\fetch\impl\DefaultFetcher;
use Plancke\HypixelPHP\HypixelPHP;

class TestUtil {

    const PLANCKE = 'f025c1c7f55a4ea0b8d93f47d17dfe0f';

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @return HypixelPHP
     */
    public static function getHypixelPHP() {
        /** @noinspection PhpUnhandledExceptionInspection */
        $HypixelPHP = new HypixelPHP('');

        $HypixelPHP->setLogger(new CustomLogger($HypixelPHP));
        $HypixelPHP->setCacheHandler(new NoCacheHandler($HypixelPHP));

        $fetcher = new DefaultFetcher($HypixelPHP);
        $fetcher->setUseCurl(false);
        $HypixelPHP->setFetcher($fetcher);

        return $HypixelPHP;
    }

}