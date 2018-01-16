<?php

namespace Plancke\HypixelPHP\log\impl;

use Plancke\HypixelPHP\log\Formatter;

/**
 * Class DefaultFormatter
 * @package Plancke\HypixelPHP\log\impl
 */
class DefaultFormatter extends Formatter {

    /**
     * @param string $line
     * @return string
     */
    public function formatLine($line) {
        return '[' . date("d-m-Y H:i:s") . '] ' . $line;
    }

}