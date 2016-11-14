<?php

namespace Plancke\Tests\util;

use Plancke\HypixelPHP\log\Logger;

class CustomLogger extends Logger {

    public function log($line) {
        error_log($line);
    }

}