<?php

namespace Plancke\Tests\util;

use Plancke\HypixelPHP\log\Logger;

/**
 * Class CustomLogger
 * @package Plancke\Tests\util
 *
 * Logger that redirects the output to error_out for testing
 */
class CustomLogger extends Logger {

    public function actuallyLog($line) {
        error_log($line);
    }
}