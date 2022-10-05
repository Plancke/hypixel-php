<?php

namespace Plancke\HypixelPHP\classes\serverType;

/**
 * Class GameType
 * @package Plancke\HypixelPHP\classes\serverType
 */
class ServerType {

    protected $enum, $db, $name, $short, $id;

    /**
     * @param string $enum
     * @param string $db
     * @param string $name
     * @param string $short
     * @param int $id
     */
    public function __construct($enum, $db, $name, $short, $id) {
        $this->enum = $enum;
        $this->db = $db;
        $this->name = $name;
        $this->short = $short;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEnum() {
        return $this->enum;
    }

    /**
     * @return string
     */
    public function getDb() {
        return $this->db;
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
    public function getShort() {
        return $this->short;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

}