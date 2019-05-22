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
     * @throws HypixelPHPException
     */
    public function getAchievementPoints($force_update = false) {
        if (!$force_update) {
            return $this->getExtra('achievements.points', 0);
        }

        $achievements = $this->getHypixelPHP()->getResourceManager()->getGeneralResources()->getAchievements()['achievements'];
        $games = array_keys($achievements);

        $total = 0;
        $oneTime = $this->getArray('achievementsOneTime');
        $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Starting OneTime Achievements');
        foreach ($oneTime as $dbName) {
            if (!is_string($dbName)) continue;
            $game = strtolower(substr($dbName, 0, strpos($dbName, "_")));
            $dbName = strtoupper(substr($dbName, strpos($dbName, "_") + 1));
            if (!in_array($game, $games)) continue;
            $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Achievement: ' . strtoupper(substr($dbName, strpos($dbName, "_"))));
            if (in_array($dbName, array_keys($achievements[$game]['one_time']))) {
                if (array_key_exists("legacy", $achievements[$game]['one_time'][$dbName]) && $achievements[$game]['one_time'][$dbName]["legacy"]) continue;
                $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Achievement: ' . $dbName . ' - ' . $achievements[$game]['one_time'][$dbName]['points']);
                $total += $achievements[$game]['one_time'][$dbName]['points'];
            }
        }
        $tiered = $this->getArray('achievements');
        $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Starting Tiered Achievements');
        foreach ($tiered as $dbName => $value) {
            $game = strtolower(substr($dbName, 0, strpos($dbName, "_")));
            $dbName = strtoupper(substr($dbName, strpos($dbName, "_") + 1));
            if (!in_array($game, $games)) continue;
            $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Achievement: ' . $dbName);
            if (in_array($dbName, array_keys($achievements[$game]['tiered']))) {
                if (array_key_exists("legacy", $achievements[$game]['tiered'][$dbName]) && $achievements[$game]['tiered'][$dbName]["legacy"]) continue;
                $tierTotal = 0;
                foreach ($achievements[$game]['tiered'][$dbName]['tiers'] as $tier) {
                    if ($value >= $tier['amount']) {
                        $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Tier: ' . $tier['amount'] . ' - ' . $tier['points']);
                        $tierTotal += $tier['points'];
                    }
                }
                $total += $tierTotal;
            }
        }

        //store extra
        $this->setExtra(['achievements' => ['points' => $total, 'timestamp' => time()]]);
        return $total;
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

    function getCacheTimeKey() {
        return CacheTimes::PLAYER;
    }

}