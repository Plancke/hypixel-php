<?php

namespace Plancke\HypixelPHP\classes;

use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\util\Utilities;

/**
 * Class APIObject
 * @package Plancke\HypixelPHP\classes
 */
abstract class APIObject extends APIHolding {

    protected $data;

    /**
     * APIObject constructor.
     * @param HypixelPHP $HypixelPHP
     * @param array $data
     */
    public function __construct(HypixelPHP $HypixelPHP, $data) {
        parent::__construct($HypixelPHP);

        $this->data = $data;
    }

    /**
     * @param $key
     * @return array
     */
    public function getArray($key) {
        return $this->get($key, []);
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
    public function getData() {
        return $this->getRaw();
    }

    /**
     * @return array
     */
    public function getRaw() {
        return $this->data;
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
     * @param $key
     * @param double $default
     * @return double
     */
    public function getDouble($key, $default = 0.0) {
        return $this->get($key, $default);
    }
}