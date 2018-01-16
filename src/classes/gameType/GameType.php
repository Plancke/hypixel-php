<?php

namespace Plancke\HypixelPHP\classes\gameType;

/**
 * Class GameType
 * @package Plancke\HypixelPHP\classes\gameType
 */
class GameType {

    protected $enum, $db, $name, $short, $id, $boosters;

    /**
     * @param string $enum
     * @param string $db
     * @param string $name
     * @param string $short
     * @param int $id
     * @param bool $boosters
     */
    public function __construct($enum, $db, $name, $short, $id, $boosters = true) {
        $this->enum = $enum;
        $this->db = $db;
        $this->name = $name;
        $this->short = $short;
        $this->id = $id;
        $this->boosters = $boosters;
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

    /**
     * @return boolean
     */
    public function hasBoosters() {
        return $this->boosters;
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'enum' => $this->enum,
            'id' => $this->id,
            'name' => $this->name,
            'short' => $this->short,
            'db' => $this->db
        ];
    }
}