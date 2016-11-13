<?php

namespace Plancke\HypixelPHP\classes\gameType;

class GameType {
    private $db, $name, $short, $id, $boosters;

    /**
     * @param $db
     * @param $name
     * @param $short
     * @param $id
     * @param bool $boosters
     */
    public function __construct($db, $name, $short, $id, $boosters = true) {
        $this->db = $db;
        $this->name = $name;
        $this->short = $short;
        $this->id = $id;
        $this->boosters = $boosters;
    }

    public function getDb() {
        return $this->db;
    }

    public function getName() {
        return $this->name;
    }

    public function getShort() {
        return $this->short;
    }

    public function getId() {
        return $this->id;
    }

    public function hasBoosters() {
        return $this->boosters;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short' => $this->short,
            'db' => $this->db
        ];
    }
}