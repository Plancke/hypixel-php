<?php

namespace Plancke\HypixelPHP\responses\player;

use Plancke\HypixelPHP\util\Utilities;

class Rank {
    protected $name, $id, $options, $staff;

    /**
     * @param $id
     * @param $name
     * @param $options
     * @param bool $staff
     */
    public function __construct($id, $name, $options, $staff = false) {
        $this->id = $id;
        $this->name = $name;
        $this->options = $options;
        $this->staff = $staff;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCleanName() {
        if ($this->name == 'NON_DONOR' || $this->name == 'NONE') return 'DEFAULT';
        if ($this->name == 'SUPERSTAR') return 'MVP++';
        return str_replace("_", ' ', str_replace('_PLUS', '+', $this->name));
    }

    public function getOptions() {
        return $this->options;
    }

    public function isStaff() {
        return $this->staff;
    }

    public function getPrefix(Player $player) {
        if ($player->get("rankPlusColor") != null) {
            $plusColor = Utilities::MC_COLORNAME[$player->get("rankPlusColor")];
            if ($plusColor != null) {
                if ($this->id == RankTypes::MVP_PLUS) {
                    return '§b[MVP' . $plusColor . '+§b]';
                } else if ($this->id == RankTypes::SUPERSTAR) {
                    return '§6[MVP' . $plusColor . '++§6]';
                }
            }
        }
        return isset($this->options['prefix']) ? $this->options['prefix'] : null;
    }

    public function getColor() {
        return isset($this->options['color']) ? $this->options['color'] : null;
    }

    public function getMultiplier() {
        return isset($this->options['eulaMultiplier']) ? $this->options['eulaMultiplier'] : 1;
    }

    public function __toString() {
        return json_encode([$this->name => $this->options]);
    }
}
