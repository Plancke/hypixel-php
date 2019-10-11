<?php

namespace Plancke\HypixelPHP\classes;

use Plancke\HypixelPHP\util\Utilities;

/**
 * Class DataHolding
 * @package Plancke\HypixelPHP\classes
 */
trait DataHolding {

    /**
     * @param $key
     * @param array $default
     * @return array
     */
    public function getArray($key, $default = []) {
        $ret = $this->get($key, $default);
        if (is_array($ret)) return $ret;
        return $default;
    }

    /**
     * @param      $key
     * @param null $default
     * @param string $delimiter
     * @return mixed
     */
    public function get($key, $default = null, $delimiter = '.') {
        if (!is_array($this->getData())) {
            return $default;
        }
        return Utilities::getRecursiveValue($this->getData(), $key, $default, $delimiter);
    }

    /**
     * @return array
     */
    abstract function getData();

    /**
     * @param $key
     * @param int $default
     * @return int
     */
    public function getInt($key, $default = 0) {
        $ret = $this->get($key, $default);
        if (is_int($ret)) return $ret;
        return $default;
    }

    /**
     * @param $key
     * @param double $default
     * @return double
     */
    public function getDouble($key, $default = 0.0) {
        $ret = $this->get($key, $default);
        if (is_double($ret)) return $ret;
        return $default;
    }

    /**
     * @param $key
     * @param $default
     * @return number
     */
    public function getNumber($key, $default = 0) {
        $ret = $this->get($key, $default);
        if (is_numeric($ret)) return $ret + 0;
        return $default;
    }
}