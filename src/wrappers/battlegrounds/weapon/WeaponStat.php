<?php

namespace Plancke\HypixelPHP\wrappers\battlegrounds\weapon;

class WeaponStat {
    protected $name, $field, $upgrade, $id;

    /**
     * WeaponStat constructor.
     * @param string $name
     * @param string $field
     * @param double $upgrade
     * @param int $id
     */
    function __construct($name, $field, $upgrade, $id) {
        $this->name = $name;
        $this->field = $field;
        $this->upgrade = $upgrade;
        $this->id = $id;
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

}