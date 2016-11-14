<?php

namespace Plancke\HypixelPHP\classes;

use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\util\Utilities;

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
     * @return mixed
     */
    public function getData() {
        return $this->getRaw();
    }

    public function getRaw() {
        return $this->data;
    }

    /**
     * @param $key
     * @return array
     */
    public function getArray($key) {
        return $this->get($key, []);
    }

}