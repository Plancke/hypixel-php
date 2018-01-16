<?php

namespace Plancke\HypixelPHP\log;

/**
 * Class Formatter
 * @package Plancke\HypixelPHP\log
 */
abstract class Formatter {

    /**
     * @param string $line
     * @return string
     */
    public abstract function formatLine($line);

}