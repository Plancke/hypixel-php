<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds\weapon;

class WeaponStat {
    protected $name, $field, $upgrade, $id, $min, $max;

    /**
     * WeaponStat constructor.
     * @param string $name
     * @param string $field
     * @param double $upgrade
     * @param int $id
     */
    function __construct($name, $field, $upgrade, $id, $min, $max) {
        $this->name = $name;
        $this->field = $field;
        $this->upgrade = $upgrade;
        $this->id = $id;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getField() {
        return $this->field;
    }

    /**
     * @return double
     */
    public function getUpgrade() {
        return $this->upgrade;
    }

    /**
     * @return int
     */
    public function getID() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMin() {
        return $this->min;
    }

    /**
     * @return mixed
     */
    public function getMax() {
        return $this->max;
    }

}