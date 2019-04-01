<?php

namespace Plancke\Tests\util;

use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\impl\DefaultFetcher;
use Plancke\HypixelPHP\HypixelPHP;

class TestUtil {

    const PLANCKE = 'f025c1c7f55a4ea0b8d93f47d17dfe0f';

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @return HypixelPHP
     * @throws HypixelPHPException
     */
    public static function getHypixelPHP() {
        $HypixelPHP = new HypixelPHP(self::getAPIKey());

        $HypixelPHP->setLogger(new CustomLogger($HypixelPHP));
        $HypixelPHP->setCacheHandler(new NoCacheHandler($HypixelPHP));

        $fetcher = new DefaultFetcher($HypixelPHP);
        $fetcher->setUseCurl(false);
        $HypixelPHP->setFetcher($fetcher);

        return $HypixelPHP;
    }

    public static function getAPIKey() {
        if (isset($_ENV['API_KEY'])) return $_ENV['API_KEY'];
        if (isset($_SERVER['API_KEY'])) return $_SERVER['API_KEY'];
        return null;
    }

}