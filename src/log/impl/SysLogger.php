<?php

namespace Plancke\HypixelPHP\log\impl;

use Plancke\HypixelPHP\log\Logger;

/**
 * Class SysLogger
 * @package Plancke\HypixelPHP\log\impl
 *
 * Logger that redirects the output to syslog
 */
class SysLogger extends Logger {

    public function actuallyLog($level, $line) {
        syslog($level, $line);
    }

}