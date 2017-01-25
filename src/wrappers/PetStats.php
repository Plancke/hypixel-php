<?php

namespace Plancke\HypixelPHP\wrappers;

/**
 * Class used to interpret Hypixel's Pet system and
 * provide functions to easily get a pet's level and attributes
 *
 * @author Plancke
 * @version 1.0.0
 * @link https://plancke.io
 *
 */
class PetStats {

    const LEVELS = [
        200, 210, 230, 250, 280, 310, 350, 390, 450, 500, 570, 640, 710, 800, 880, 980, 1080, 1190, 1300, 1420,
        1540, 1670, 1810, 1950, 2100, 2260, 2420, 2580, 2760, 2940, 3120, 3310, 3510, 3710, 3920, 4140, 4360, 4590,
        4820, 5060, 5310, 5560, 5820, 6090, 6360, 6630, 6920, 7210, 7500, 7800, 8110, 8420, 8740, 9070, 9400, 9740,
        10080, 10430, 10780, 11150, 11510, 11890, 12270, 12650, 13050, 13440, 13850, 14260, 14680, 15100, 15530,
        15960, 16400, 16850, 17300, 17760, 18230, 18700, 19180, 19660, 20150, 20640, 21150, 21650, 22170, 22690,
        23210, 23750, 24280, 24830, 25380, 25930, 26500, 27070, 27640, 28220, 28810, 29400, 30000
    ];

    /**
     * @var Pet[]
     */
    private $PET_MAP = [];

    function __construct($PET_STATS) {
        foreach ($PET_STATS as $PET => $PET_INFO) {
            $this->PET_MAP[$PET] = new Pet($PET_INFO);
        }
    }

    /**
     * Calculate total amount of experience to reach given pet level
     *
     * @param $level
     * @return int
     */
    static function getExperienceUntilLevel($level) {
        $exp = 0;
        for ($i = 0; $i < min($level - 1, 100); $i++) {
            $exp += PetStats::LEVELS[$i];
        }

        return $exp;
    }

    /**
     * @param $PET
     * @return Pet
     */
    function getPet($PET) {
        return $this->PET_MAP[$PET];
    }

    /**
     * @return Pet[]
     */
    function getAllPets() {
        return $this->PET_MAP;
    }
}

class Pet {

    private $PET_STATS;
    private $LEVEL;

    function __construct($PET_STATS) {
        $this->PET_STATS = $PET_STATS;

        $this->updateLevel();
    }

    /**
     * Internally update level
     */
    function updateLevel() {
        $this->LEVEL = 1;
        $curExp = $this->getExperience();
        foreach (PetStats::LEVELS as $EXP_LEVEL) {
            if ($curExp < $EXP_LEVEL) {
                break;
            } else {
                $curExp -= $EXP_LEVEL;
                $this->LEVEL++;
            }
        }
    }

    /**
     * Get current pet experience
     *
     * @return int
     */
    function getExperience() {
        return array_key_exists('experience', $this->PET_STATS) ? $this->PET_STATS['experience'] : 0;
    }

    /**
     * Gets the value for $ATTRIBUTE_TYPE for this pet
     * taking into account the last timestamp to modify
     * the value
     *
     * @param $PET_ATTRIBUTE_TYPE
     * @return int
     */
    function getAttributeValue($PET_ATTRIBUTE_TYPE) {
        switch ($PET_ATTRIBUTE_TYPE) {
            case PetAttributeType::THIRST:
                return $this->modifyAttributeValue($PET_ATTRIBUTE_TYPE, $this->PET_STATS['THIRST']);
            case PetAttributeType::HUNGER:
                return $this->modifyAttributeValue($PET_ATTRIBUTE_TYPE, $this->PET_STATS['HUNGER']);
            case PetAttributeType::EXERCISE:
                return $this->modifyAttributeValue($PET_ATTRIBUTE_TYPE, $this->PET_STATS['EXERCISE']);
        }
        return null;
    }

    /**
     * Get value for given attribute while taking the last update
     * timestamp into account
     *
     * @param $ATTRIBUTE_TYPE
     * @param $ATTRIBUTE
     * @return int
     */
    function modifyAttributeValue($ATTRIBUTE_TYPE, $ATTRIBUTE) {
        $currentTime = round(microtime(true) * 1000);
        $timestamp = $ATTRIBUTE['timestamp'];
        $value = $ATTRIBUTE['value'];

        $timeElapsed = $currentTime - $timestamp;
        $minutesPassed = $timeElapsed / (1000 * 60);
        $iterations = floor($minutesPassed / 5);

        return max(0, round($value - $iterations * PetAttributeType::getDecay($ATTRIBUTE_TYPE)));
    }

    /**
     *
     * Get pet level
     *
     * @return int
     */
    function getLevel() {
        return $this->LEVEL;
    }

    function getLevelProgress() {
        return $this->getExperience() - PetStats::getExperienceUntilLevel($this->LEVEL);
    }

}

class PetAttributeType {

    const THIRST = 1;
    const HUNGER = 2;
    const EXERCISE = 3;


    /**
     *
     * Get decay rate for given type
     *
     * @param $ATTRIBUTE_TYPE
     * @return int
     */
    static function getDecay($ATTRIBUTE_TYPE) {
        return 1;
    }
}