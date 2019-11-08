<?php

namespace Plancke\HypixelPHP\util\games\skywars;

/**
 * Class ExpCalculator
 * @package Plancke\HypixelPHP\util\games\skywars
 */
class ExpCalculator {

    const EASY_LEVEL_EXP = [
        0, // Level 1
        20, //20
        50, //70
        80, //150
        100,//250
        250,//500
        500,//1000
        1000,//2000
        1500,//3500
        2500,//6000
        4000,//10000
        5000//15000
    ];
    const EXP_PER_LEVEL = 10000;

    public function getProgressCurrentLevel($exp) {
        $level = $this->getLevelForExp($exp);
        $levelExp = $this->getTotalExpForLevel($level);
        return $exp - $levelExp;
    }

    public function getLevelForExp($exp) {
        $easyLevelsCount = sizeof(self::EASY_LEVEL_EXP);

        $easyLevelExp = 0;
        for ($i = 1; $i <= $easyLevelsCount; $i++) {
            $expPerLevel = $this->getExpForLevel($i);
            $easyLevelExp += $expPerLevel;
            if ($exp < $easyLevelExp) {
                return $i - 1;//57965
            }
        }
        $extraLevels = ($exp - $easyLevelExp) / self::EXP_PER_LEVEL;
        return $easyLevelsCount + $extraLevels;
    }

    public function getExpForLevel($level) {
        if ($level <= sizeof(self::EASY_LEVEL_EXP)) {
            return self::EASY_LEVEL_EXP[$level - 1];
        }

        return self::EXP_PER_LEVEL;
    }

    public function getTotalExpForLevel($level) {
        $easyLevelsCount = sizeof(self::EASY_LEVEL_EXP);

        $totalExp = 0;
        $easyLevels = min($level, $easyLevelsCount);
        for ($i = 0; $i < $easyLevels; $i++) {
            $totalExp += self::EASY_LEVEL_EXP[$i];
        }

        if ($level > $easyLevelsCount) {
            $extraLevels = $level - $easyLevelsCount;
            $totalExp += ($extraLevels * self::EXP_PER_LEVEL);
        }
        return $totalExp;
    }

    /**
     * @param $prestiges array format from resources files
     * @param $level
     * @return mixed
     */
    public function getPrestigeForLevel($prestiges, $level) {
        foreach ($prestiges as $i => $prestige) {
            $nextPrestige = $prestiges[$i + 1];
            if ($level >= $prestige["RequiredLevel"] && $level < $nextPrestige["RequiredLevel"]) {
                return $prestige;
            }
        }

        return end($prestiges);
    }

}