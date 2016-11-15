<?php

namespace Plancke\HypixelPHP\wrappers;

/**
 * Class used to parse TurboKart Racer's kart system and
 * provide functions to get levels for attributes, it's name, etc...
 *
 * @author Plancke
 * @version 1.0.0
 * @link https://plancke.io
 *
 */
class Kart {
    public $ENGINE;
    public $TURBOCHARGER;
    public $FRAME;

    function __construct($ENGINE, $TURBOCHARGER, $FRAME) {
        $this->ENGINE = $this->convert($ENGINE)['GingerbreadPart'];
        $this->TURBOCHARGER = $this->convert($TURBOCHARGER)['GingerbreadPart'];
        $this->FRAME = $this->convert($FRAME)['GingerbreadPart'];
    }

    function convert($input) {
        if (is_string($input)) {
            return json_decode($this->fix_json($input), true);
        }
        return $input;
    }

    function fix_json($s) {
        $s = preg_replace('/(\w+):/i', '"\1":', $s);
        $s = preg_replace('/:(\w+)/i', ':"\1"', $s);
        return $s;
    }

    function getEngine() {
        return new Engine($this->ENGINE);
    }

    function getTurbocharger() {
        return new Turbocharger($this->TURBOCHARGER);
    }

    function getFrame() {
        return new Frame($this->FRAME);
    }
}

class Part {
    private $PART;
    private $PREFIXES = [
        "Default",
        "Starter",
        "Mini",

        "Auxiliary",
        "Standard",
        "Primary",
        "Experimental",

        "Dynamic",
        "Stellar",
        "Kinetic",
        "Multi-phase",

        "Turbocharged",
        "Quantum",
        "Superluminal",
        "Psi",

        "Eternal"
    ];

    function __construct($PART) {
        $this->PART = $PART;
    }

    function getAttributes() {
        return $this->PART['Attributes'];
    }

    function getColor() {
        $level = $this->getLevel();
        if ($level == 15) {
            return '§5';
        } else if ($level >= 11 && $level <= 14) {
            return '§d';
        } else if ($level >= 7 && $level <= 10) {
            return '§9';
        } else if ($level >= 3 && $level <= 6) {
            return '§a';
        }
        return '§7';
    }

    function getLevel() {
        $LEVEL = 0;
        if ($this->PART == null) return 0;
        if (!array_key_exists('Attributes', $this->PART)) return $LEVEL;
        foreach ($this->PART['Attributes'] as $ATTRIBUTE) {
            $LEVEL += $ATTRIBUTE['Level'];
        }
        return $LEVEL;
    }

    function getName() {
        return $this->getPrefix() . ' ' . ucfirst(strtolower($this->getRarity())) . ' ' . ucfirst(strtolower($this->getType()));
    }

    function getPrefix() {
        return $this->PREFIXES[$this->getLevel()];
    }

    function getRarity() {
        return $this->PART['PartRarity'];
    }

    function getType() {
        return $this->PART['PartType'];
    }

    function getAttributeLevel($TYPE) {
        if ($this->PART == null) return 0;
        if (!array_key_exists('Attributes', $this->PART)) return 0;
        foreach ($this->PART['Attributes'] as $ATTRIBUTE) {
            if ($ATTRIBUTE['KartAttributeType'] == $TYPE) {
                return $ATTRIBUTE['Level'];
            }
        }
        return 0;
    }
}

class Engine extends Part {
    function getRecovery() {
        return $this->getAttributeLevel('RECOVERY');
    }

    function getTopSpeed() {
        return $this->getAttributeLevel('TOP_SPEED');
    }

    function getAcceleration() {
        return $this->getAttributeLevel('ACCELERATION');
    }
}

class Turbocharger extends Part {
    function getDriftingEfficiency() {
        return $this->getAttributeLevel('DRIFTING_EFFICIENCY');
    }

    function getBrakes() {
        return $this->getAttributeLevel('BRAKES');
    }

    function getBoosterSpeed() {
        return $this->getAttributeLevel('BOOSTER_SPEED');
    }
}

class Frame extends Part {
    function getStartPosition() {
        return $this->getAttributeLevel('START_POSITION');
    }

    function getTraction() {
        return $this->getAttributeLevel('TRACTION');
    }

    function getHandling() {
        return $this->getAttributeLevel('HANDLING');
    }
}