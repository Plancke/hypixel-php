<?php

namespace Plancke\HypixelPHP\log\impl;

use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\log\Logger;

/**
 * Class NoLogger
 * @package Plancke\HypixelPHP\log\impl
 */
class NoLogger extends Logger {

    public function __construct(HypixelPHP $HypixelPHP) {
        parent::__construct($HypixelPHP);

        $this->enabled = false;
    }

    public function actuallyLog($level, $line) {
    }

}