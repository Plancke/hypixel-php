<?php

namespace Plancke\HypixelPHP\responses\player;

use Plancke\HypixelPHP\color\ColorUtils;

/**
 * Class Rank
 * @package Plancke\HypixelPHP\responses\player
 */
class Rank {
    protected $name, $id, $options, $staff;

    /**
     * @param int $id
     * @param string $name
     * @param array $options
     * @param bool $staff
     */
    public function __construct($id, $name, $options, $staff = false) {
        $this->id = $id;
        $this->name = $name;
        $this->options = $options;
        $this->staff = $staff;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
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
    public function getCleanName() {
        if ($this->name == 'NON_DONOR' || $this->name == 'NONE') return 'DEFAULT';
        if ($this->name == 'SUPERSTAR') return 'MVP++';
        return str_replace("_", ' ', str_replace('_PLUS', '+', $this->name));
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function isStaff() {
        return $this->staff;
    }

    /**
     * @param Player $player
     * @return string|null
     */
    public function getPrefix(Player $player) {
        if ($player->get("rankPlusColor") != null) {
            $plusColor = ColorUtils::NAME_TO_CODE[$player->get("rankPlusColor")];
            if ($plusColor != null) {
                if ($this->id == RankTypes::MVP_PLUS) {
                    return '§b[MVP' . $plusColor . '+§b]';
                } else if ($this->id == RankTypes::SUPERSTAR) {
                    $superStarColor = $player->getSuperStarColor();
                    if ($superStarColor == null) $superStarColor = ColorUtils::GOLD;
                    return $superStarColor . '[MVP' . $plusColor . '++' . $superStarColor . ']';
                }
            }
        }
        return isset($this->options['prefix']) ? $this->options['prefix'] : null;
    }

    /**
     * @return string|null
     */
    public function getColor() {
        return isset($this->options['color']) ? $this->options['color'] : null;
    }

    /**
     * @return int
     */
    public function getMultiplier() {
        return isset($this->options['eulaMultiplier']) ? $this->options['eulaMultiplier'] : 1;
    }

    /**
     * @return string
     */
    public function __toString() {
        return json_encode([$this->name => $this->options]);
    }
}
