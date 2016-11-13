<?php

namespace Plancke\HypixelPHP\classes;

use Plancke\HypixelPHP\HypixelPHP;

abstract class APIObject extends APIHolding {

    protected $data;

    public function __construct(HypixelPHP $HypixelPHP, $data) {
        parent::__construct($HypixelPHP);

        $this->data = $data;
    }

    /**
     * @param $key
     * @param int $default
     * @return int
     */
    public function getInt($key, $default = 0) {
        return $this->get($key, $default);
    }

    /**
     * @param      $key
     * @param null $default
     * @param bool $implicit
     * @param string $delimiter
     * @return mixed
     */
    public function get($key, $default = null, $implicit = false, $delimiter = '.') {
        if (!is_array($this->getData())) {
            return $default;
        }

        if (!$implicit) {
            return $this->getRecursiveValue($this->getData(), $key, $default, $delimiter);
        }

        return in_array($key, array_keys($this->getData())) ? $this->getData()[$key] : $default;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->getRaw();
    }

    public function getRaw() {
        return $this->data;
    }

    protected function getRecursiveValue($array, $key, $default = null, $delimiter = '.') {
        $return = $array;
        foreach (explode($delimiter, $key) as $split) {
            $return = isset($return[$split]) ? $return[$split] : $default;
        }
        return $return ? $return : $default;
    }

    /**
     * @param $key
     * @return array
     */
    public function getArray($key) {
        return $this->get($key, []);
    }

}