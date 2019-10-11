<?php

namespace Plancke\HypixelPHP\responses\player;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\color\ColorUtils;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\fetch\Response;
use Plancke\HypixelPHP\responses\booster\Booster;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\util\Leveling;
use Plancke\HypixelPHP\util\Utilities;

/**
 * Class Player
 * @package Plancke\HypixelPHP\responses\player
 */
class Player extends HypixelObject {
    protected $guild, $friends, $session;

    /**
     * Get the Stats object for the player
     *
     * @return Stats
     */
    public function getStats() {
        return new Stats($this->getHypixelPHP(), $this->getArray('stats'));
    }

    /**
     * get Player achievement points
     * @param bool $force_update
     * @return int
     * @deprecated use the new achievement data function
     */
    public function getAchievementPoints($force_update = false) {
        return Utilities::getRecursiveValue($this->getAchievementData(), 'standard.points.current', 0);
    }

    /**
     * get Player achievement points
     * @return array
     */
    public function getAchievementData() {
        $data = [
            'standard' => [
                'points' => [
                    'current' => 0,
                    'max' => 0
                ]
            ],
            'legacy' => [
                'points' => [
                    'current' => 0,
                    'max' => 0
                ]
            ],
        ];

        $isLegacy = function ($arr) {
            return is_array($arr) && array_key_exists("legacy", $arr) && $arr["legacy"];
        };

        $getDBName = function ($game, $key) {
            if (strpos($game, 'legacy') === 0) {
                $game = substr($game, strlen('legacy'));
            }
            return $game . '_' . strtolower($key);
        };

        $achievements = $this->getHypixelPHP()->getResourceManager()->getGeneralResources()->getAchievements()->getData()['achievements'];

        $oneTime = $this->getArray('achievementsOneTime');
        $tiered = $this->getArray('achievements');
        foreach ($achievements as $game => $gameAchievements) {
            $data['standard']['points']['max'] += $gameAchievements['total_points'];
            $data['legacy']['points']['max'] += $gameAchievements['total_legacy_points'];

            foreach ($gameAchievements['one_time'] as $achievementKey => $oneTimeAchievement) {
                $dbName = $getDBName($game, strtolower($achievementKey));
                $points = $oneTimeAchievement['points'];
                $legacy = $isLegacy($oneTimeAchievement);

                if (in_array($dbName, $oneTime)) {
                    if ($legacy) {
                        $data['legacy']['points']['current'] += $points;
                    } else {
                        $data['standard']['points']['current'] += $points;
                    }
                }
            }

            foreach ($gameAchievements['tiered'] as $achievementKey => $tieredAchievement) {
                $dbName = $getDBName($game, strtolower($achievementKey));
                $legacy = $isLegacy($tieredAchievement);
                $value = Utilities::getRecursiveValue($tiered, $dbName, 0);
                foreach ($tieredAchievement['tiers'] as $tier) {
                    $points = $tier['points'];

                    if ($value >= $tier['amount']) {
                        if ($legacy) {
                            $data['legacy']['points']['current'] += $points;
                        } else {
                            $data['standard']['points']['current'] += $points;
                        }
                    }
                }
            }
        }

        // legacy compatibility
        $data['points'] = $data['standard']['points']['current'];

        return $data;
    }

    /**
     * @return Session|Response|null
     * @throws HypixelPHPException
     */
    public function getSession() {
        if ($this->session == null) {
            $this->session = $this->getHypixelPHP()->getSession([FetchParams::SESSION_BY_UUID => $this->getUUID()]);
        }
        return $this->session;
    }

    /**
     * Get player UUID
     *
     * @return string
     */
    public function getUUID() {
        return $this->get('uuid');
    }

    /**
     * @return Friends|Response|null
     * @throws HypixelPHPException
     */
    public function getFriends() {
        if ($this->friends == null) {
            $this->friends = $this->getHypixelPHP()->getFriends([FetchParams::FRIENDS_BY_UUID => $this->getUUID()]);
        }
        return $this->friends;
    }

    /**
     * @return Booster[]
     * @throws HypixelPHPException
     */
    public function getBoosters() {
        $BOOSTERS = $this->getHypixelPHP()->getBoosters();
        if ($BOOSTERS instanceof Boosters) {
            return $BOOSTERS->getBoosters($this->getUUID());
        }
        return [];
    }

    /**
     * @param bool $prefix
     * @param bool $guildTag
     * @return string
     * @throws HypixelPHPException
     */
    public function getRawFormattedName($prefix = true, $guildTag = false) {
        $rank = $this->getRank(false);
        $out = $rank->getColor() . $this->getName();
        if ($prefix) {
            $out = ($this->getPrefix() != null ? $this->getPrefix() : $rank->getPrefix($this)) . ' ' . $this->getName();
        }
        if ($guildTag) {
            if ($this->getGuildTag() != null) {
                $color = '§7';
                if ($this->getGuild()->getTagColor() != null) {
                    $color = $this->getGuild()->getTagColor();
                }

                $out .= $color . ' [' . $this->getGuildTag() . ']';
            }
        }
        return $out;
    }

    /**
     * get Rank
     * @param bool $package
     * @param array $rankKeys
     * @return Rank
     */
    public function getRank($package = true, $rankKeys = ['monthlyPackageRank', 'newPackageRank', 'packageRank']) {
        /** @var $returnRank Rank */
        $returnRank = null;
        if ($package) {
            foreach ($rankKeys as $key) {
                /** @var $rank Rank */
                $rank = RankTypes::fromName($this->get($key));
                if ($rank != null) {
                    if ($returnRank == null || $rank->getId() > $returnRank->getId()) {
                        $returnRank = $rank;
                    }
                }
            }
        } else {
            if (!$this->isStaff()) return $this->getRank(true, $rankKeys);
            $returnRank = RankTypes::fromName($this->get('rank'));
        }
        if ($returnRank == null) {
            $returnRank = RankTypes::fromID(RankTypes::NON_DONOR);
        }
        return $returnRank;
    }

    /**
     * @return bool
     */
    public function isStaff() {
        $rank = $this->get('rank', 'NORMAL');
        if ($rank == 'NORMAL') {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getName() {
        if ($this->get('displayname') != null) {
            return $this->get('displayname');
        } else {
            $aliases = $this->getArray('knownAliases');
            if (sizeof($aliases) == 0) {
                return $this->get('playername');
            }
            return end($aliases);
        }
    }

    /**
     * @return string
     */
    public function getPrefix() {
        return $this->get('prefix');
    }

    /**
     * @return null|string
     * @throws HypixelPHPException
     */
    public function getGuildTag() {
        $guild = $this->getGuild();
        if ($guild instanceof Guild) {
            return $guild->getTag();
        }
        return null;
    }

    /**
     * @return Guild|Response|null
     * @throws HypixelPHPException
     */
    public function getGuild() {
        if ($this->guild == null) {
            $this->guild = $this->getHypixelPHP()->getGuild([FetchParams::GUILD_BY_PLAYER_UUID => $this->getUUID()]);
        }
        return $this->guild;
    }

    /**
     * @return string
     */
    public function getSuperStarColor() {
        $color = $this->get('monthlyRankColor');
        if ($color == null) return null;
        if (isset(ColorUtils::NAME_TO_CODE[$color])) {
            return ColorUtils::NAME_TO_CODE[$color];
        }
        return ColorUtils::GOLD;
    }

    /**
     * Check if player has a PreEULA rank
     *
     * @return bool
     */
    public function isPreEULA() {
        return $this->get('packageRank', null) != null;
    }

    /**
     * get Current Multiplier, accounts for level and Pre-EULA rank
     * @return int
     */
    public function getMultiplier() {
        $rankMultiplier = 0;
        if ($this->isStaff()) {
            $rankMultiplier = $this->getRank(false)->getMultiplier();
        }
        $pre = $this->getRank(true, ['packageRank']); // only old rank matters
        $eulaMultiplier = $pre != null ? $pre->getMultiplier() : 1;
        $levelMultiplier = $this->getLevelMultiplier($this->getLevel()) + 1;
        $highest = max($rankMultiplier, $eulaMultiplier, $levelMultiplier);
        return $highest;
    }

    public function getLevelMultiplier($level) {
        if ($level >= 250) return 7;
        if ($level >= 200) return 6;
        if ($level >= 150) return 5.5;
        if ($level >= 125) return 5;
        if ($level >= 100) return 4.5;
        if ($level >= 50) return 4;
        if ($level >= 40) return 3.5;
        if ($level >= 30) return 3;
        if ($level >= 25) return 2.5;
        if ($level >= 20) return 2;
        if ($level >= 15) return 1.5;
        if ($level >= 10) return 1;
        if ($level >= 5) return 0.5;
        return 0;
    }

    /**
     * Get full Level of player
     * @return int
     */
    public function getLevel() {
        return Leveling::getLevel(Leveling::getExperience($this));
    }

    /**
     * Get exact Level of player
     * @return double
     */
    public function getExactLevel() {
        return Leveling::getExactLevel(Leveling::getExperience($this));
    }

    public function getCacheTimeKey() {
        return CacheTimes::PLAYER;
    }

    public function save() {
        $this->getHypixelPHP()->getCacheHandler()->setPlayer($this);
    }
}