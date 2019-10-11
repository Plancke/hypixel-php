<?php

namespace Plancke\Tests\util;

use Plancke\HypixelPHP\cache\impl\NoCacheHandler;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\impl\DefaultFetcher;
use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\log\Logger;

class TestUtil {

    /**
     * @return HypixelPHP
     * @throws HypixelPHPException
     */
    public static function getHypixelPHP() {
        $HypixelPHP = new HypixelPHP(self::getAPIKey());

        $HypixelPHP->setLogger(new class ($HypixelPHP) extends Logger {
            public function actuallyLog($level, $line) {
                echo $level . ': ' . $line . "\n";
            }
        });
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