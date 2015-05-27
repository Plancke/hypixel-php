<?php

/**
 * Class Kart
 * @author Plancke
 *
 */
class Kart
{
    public $ENGINE;
    public $TURBOCHARGER;
    public $FRAME;

    function __construct($ENGINE, $TURBOCHARGER, $FRAME)
    {
        $this->ENGINE = $this->convert($ENGINE);
        $this->TURBOCHARGER = $this->convert($TURBOCHARGER);
        $this->FRAME = $this->convert($FRAME);
    }

    function convert($input)
    {
        if (is_string($input)) {
            return json_decode($input, true);
        }
        return $input;
    }

    function getEngine()
    {
        return new Engine($this->ENGINE);
    }

    function getTurbocharger()
    {
        return new Turbocharger($this->TURBOCHARGER);
    }

    function getFrame()
    {
        return new Frame($this->FRAME);
    }
}

class Part
{
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

    function __construct($PART)
    {
        $this->PART = $PART;
    }

    function getType()
    {
        return $this->PART['PartType'];
    }

    function getRarity()
    {
        return $this->PART['PartRarity'];
    }

    function getAttributes()
    {
        return $this->PART['Attributes'];
    }

    function getLevel()
    {
        $LEVEL = 0;
        foreach ($this->PART['Attributes'] as $ATTRIBUTE) {
            $LEVEL += $ATTRIBUTE['Level'];
        }
        return $LEVEL;
    }

    function getColor()
    {
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

    function getPrefix()
    {
        return $this->PREFIXES[$this->getLevel()];
    }

    function getAttributeLevel($TYPE)
    {
        foreach ($this->PART['Attributes'] as $ATTRIBUTE) {
            if ($ATTRIBUTE['KartAttributeType'] == $TYPE) {
                return $ATTRIBUTE['Level'];
            }
        }
        return 0;
    }
}

class Engine extends Part
{
    function getRecovery()
    {
        return $this->getAttributeLevel('RECOVERY');
    }

    function getTopSpeed()
    {
        return $this->getAttributeLevel('TOP_SPEED');
    }

    function getAcceleration()
    {
        return $this->getAttributeLevel('ACCELERATION');
    }
}

class Turbocharger extends Part
{
    function getDriftingEfficiency()
    {
        return $this->getAttributeLevel('DRIFTING_EFFICIENCY');
    }

    function getBrakes()
    {
        return $this->getAttributeLevel('BRAKES');
    }

    function getBoosterSpeed()
    {
        return $this->getAttributeLevel('BOOSTER_SPEED');
    }
}

class Frame extends Part
{
    function getStartPosition()
    {
        return $this->getAttributeLevel('START_POSITION');
    }

    function getTraction()
    {
        return $this->getAttributeLevel('TRACTION');
    }

    function getHandling()
    {
        return $this->getAttributeLevel('HANDLING');
    }
}