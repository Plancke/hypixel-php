<?php

namespace Plancke\HypixelPHP\responses\player;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\color\ColorUtils;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\fetch\Response;
use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\responses\booster\Booster;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\RecentGames;
use Plancke\HypixelPHP\responses\Status;
use Plancke\HypixelPHP\util\CachedGetter;
use Plancke\HypixelPHP\util\Leveling;
use Plancke\HypixelPHP\util\Utilities;

/**
 * Class Player
 * @package Plancke\HypixelPHP\responses\player
 */
class Player extends HypixelObject {

    /**
     * @var CachedGetter $guild
     * @var CachedGetter $friends
     * @var CachedGetter $status
     * @var CachedGetter $boosters
     * @var CachedGetter $recentGames
     */
    protected $guild, $friends, $status, $boosters, $recentGames;

    /**
     * @param            $data
     * @param HypixelPHP $HypixelPHP
     */
    public function __construct(HypixelPHP $HypixelPHP, $data) {
        parent::__construct($HypixelPHP, $data);

        $player = $this;
        $this->guild = new CachedGetter(function () use ($player) {
            return $player->getHypixelPHP()->getGuild([FetchParams::GUILD_BY_PLAYER_UUID => $player->getUUID()]);
        });
        $this->friends = new CachedGetter(function () use ($player) {
            return $player->getHypixelPHP()->getFriends([FetchParams::FRIENDS_BY_UUID => $player->getUUID()]);
        });
        $this->status = new CachedGetter(function () use ($player) {
            // the timestamps indicate player is offline, don't bother requesting status
            // both will be 0 for staff, so we're stuck always pulling status
            if ($player->getInt("lastLogin") < $player->getInt("lastLogout")) return null;

            // actually request the status
            return $player->getHypixelPHP()->getStatus([FetchParams::STATUS_BY_UUID => $player->getUUID()]);
        });
        $this->recentGames = new CachedGetter(function () use ($player) {
            // actually request the status
            return $player->getHypixelPHP()->getRecentGames([FetchParams::RECENT_GAMES_BY_UUID => $player->getUUID()]);
        });
        $this->boosters = new CachedGetter(/** @throws HypixelPHPException */ function () use ($player) {
            $player = $this->getHypixelPHP()->getBoosters();
            if ($player instanceof Boosters) {
                return $player->getBoosters($this->getUUID());
            }
            return [];
        });
    }


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
     * @return array
     */
    public function getAchievementData() {
        $data = [
            '_total' => [
                'points' => [
                    'current' => 0,
                    'max' => 0
                ]
            ],
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

        $getKey = function ($arr) {
            if (is_array($arr)) {
                if (array_key_exists("legacy", $arr) && $arr["legacy"]) return "legacy";
            }
            return "standard";
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
                $pointsKey = $getKey($oneTimeAchievement);

                if (in_array($dbName, $oneTime)) {
                    $data[$pointsKey]['points']['current'] += $points;
                }
            }

            foreach ($gameAchievements['tiered'] as $achievementKey => $tieredAchievement) {
                $dbName = $getDBName($game, strtolower($achievementKey));
                $pointsKey = $getKey($tieredAchievement);
                $value = Utilities::getRecursiveValue($tiered, $dbName, 0);
                foreach ($tieredAchievement['tiers'] as $tier) {
                    $points = $tier['points'];

                    if ($value >= $tier['amount']) {
                        $data[$pointsKey]['points']['current'] += $points;
                    }
                }
            }
        }

        foreach ($data as $key => $value) {
            if ($key == '_total') continue;

            $data['_total']['points']['current'] += $value['points']['current'];
            $data['_total']['points']['max'] += $value['points']['max'];
        }

        return $data;
    }

    /**
     * @return Status|Response|null
     * @throws HypixelPHPException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function getStatus() {
        return $this->status->get();
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
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function getFriends() {
        return $this->friends->get();
    }

    /**
     * @return Booster[]
     * @throws HypixelPHPException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function getBoosters() {
        return $this->boosters->get();
    }

    /**
     * @return RecentGames
     * @throws HypixelPHPException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function getRecentGames() {
        return $this->recentGames->get();
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
        return $this->guild->get();
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
        return max($rankMultiplier, $eulaMultiplier, $levelMultiplier);
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
