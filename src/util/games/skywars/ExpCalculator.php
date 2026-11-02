<?php

namespace Plancke\HypixelPHP\util\games\skywars;

/**
 * Class ExpCalculator
 * @package Plancke\HypixelPHP\util\games\skywars
 */
class ExpCalculator {

    const LEVEL_CAP = 10000;
    const EASY_LEVEL_EXP = [
        0,
        10,
        25,
        50,
        75,
        100,
        250,
        500,
        750,
        1000,
        1250,
        1500,
        1750,
        2000,
        2500,
        3000,
        3500,
        4000,
        4500,
        5000
    ];

    private int $easyLevels;
    private int $easyLevelExpTotal;
    private int $expPerLevel;

    public function __construct() {
        $this->easyLevels = sizeof(self::EASY_LEVEL_EXP);
        $this->easyLevelExpTotal = array_sum(self::EASY_LEVEL_EXP);
        $this->expPerLevel = self::EASY_LEVEL_EXP[$this->easyLevels - 1];
    }

    public function getProgressCurrentLevel($exp) {
        $level = $this->getLevelForExp($exp);
        $levelExp = $this->getTotalExpForLevel($level);
        return $exp - $levelExp;
    }

    public function getLevelForExp($exp) {
        $exp = (int)$exp;
        if ($exp < $this->easyLevelExpTotal) {
            $total = 0;
            for ($i = 1; $i < $this->easyLevels; $i++) {
                $total += self::EASY_LEVEL_EXP[$i];
                if ($exp < $total) {
                    return $i;
                }
            }
        }

        return min($this->easyLevels + intdiv($exp - $this->easyLevelExpTotal, $this->expPerLevel), self::LEVEL_CAP);
    }

    public function getExpForLevel($level) {
        if ($level < 1) {
            return 0;
        }
        if ($level >= $this->easyLevels) {
            return $this->expPerLevel;
        }
        return self::EASY_LEVEL_EXP[$level - 1];
    }

    public function getTotalExpForLevel($level) {
        if ($level <= 1) {
            return 0;
        }
        if ($level <= $this->easyLevels) {
            return array_sum(array_slice(self::EASY_LEVEL_EXP, 0, $level));
        }

        return $this->easyLevelExpTotal + ($level - $this->easyLevels) * $this->expPerLevel;
    }

    /**
     * @param $prestiges array format from resources files
     * @param $level
     * @return mixed
     */
    public function getPrestigeForLevel(array $prestiges, $level) {
        foreach (array_reverse($prestiges) as $prestige) {
            if ($level >= $prestige["requiredLevel"]) {
                return $prestige;
            }
        }

        return end($prestiges);
    }

}
