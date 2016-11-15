<?php

namespace Plancke\HypixelPHP\responses\player;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\fetch\Response;
use Plancke\HypixelPHP\responses\booster\Booster;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\util\Utilities;

class Player extends HypixelObject {
    private $guild, $friends, $session;

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
     */
    public function getAchievementPoints($force_update = false) {
        if (!$force_update) {
            return $this->getExtra('achievementPoints', 0);
        }

        $achievements = $this->getHypixelPHP()->getResourceManager()->getGeneralResources()->getAchievements()['achievements'];
        $games = array_keys($achievements);

        $total = 0;
        $oneTime = $this->getArray('achievementsOneTime');
        $this->getHypixelPHP()->getLogger()->log('Starting OneTime Achievements');
        foreach ($oneTime as $dbName) {
            if (!is_string($dbName)) {
                continue;
            }
            $game = strtolower(substr($dbName, 0, strpos($dbName, "_")));
            $dbName = strtoupper(substr($dbName, strpos($dbName, "_") + 1));
            if (!in_array($game, $games)) {
                continue;
            }
            $this->getHypixelPHP()->getLogger()->log('Achievement: ' . strtoupper(substr($dbName, strpos($dbName, "_"))));
            if (in_array($dbName, array_keys($achievements[$game]['one_time']))) {
                $this->getHypixelPHP()->getLogger()->log('Achievement: ' . $dbName . ' - ' . $achievements[$game]['one_time'][$dbName]['points']);
                $total += $achievements[$game]['one_time'][$dbName]['points'];
            }
        }
        $tiered = $this->getArray('achievements');
        $this->getHypixelPHP()->getLogger()->log('Starting Tiered Achievements');
        foreach ($tiered as $dbName => $value) {
            $game = strtolower(substr($dbName, 0, strpos($dbName, "_")));
            $dbName = strtoupper(substr($dbName, strpos($dbName, "_") + 1));
            if (!in_array($game, $games)) {
                continue;
            }
            $this->getHypixelPHP()->getLogger()->log('Achievement: ' . $dbName);
            if (in_array($dbName, array_keys($achievements[$game]['tiered']))) {
                $tierTotal = 0;
                foreach ($achievements[$game]['tiered'][$dbName]['tiers'] as $tier) {
                    if ($value >= $tier['amount']) {
                        $this->getHypixelPHP()->getLogger()->log('Tier: ' . $tier['amount'] . ' - ' . $tier['points']);
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
     * @return Guild|Response|null
     */
    public function getGuild() {
        if ($this->guild == null) {
            $this->guild = $this->getHypixelPHP()->getGuild([FetchParams::GUILD_BY_PLAYER_UUID => $this->getUUID()]);
        }
        return $this->guild;
    }

    /**
     * @return Session|Response|null
     */
    public function getSession() {
        if ($this->session == null) {
            $this->session = $this->getHypixelPHP()->getSession([FetchParams::SESSION_BY_UUID => $this->getUUID()]);
        }
        return $this->session;
    }

    /**
     * @return Friends|Response|null
     */
    public function getFriends() {
        if ($this->friends == null) {
            $this->friends = $this->getHypixelPHP()->getFriends([FetchParams::FRIENDS_BY_UUID => $this->getUUID()]);
        }
        return $this->friends;
    }

    /**
     * @return Booster[]
     */
    public function getBoosters() {
        $BOOSTERS = $this->getHypixelPHP()->getBoosters();
        if ($BOOSTERS instanceof Boosters) {
            return $BOOSTERS->getBoosters($this->getUUID());
        }
        return [];
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
     * get Formatted name of Player
     *
     * @param bool $prefix
     * @param bool $guildTag
     * @param bool $parseColors
     * @return string
     */
    public function getFormattedName($prefix = true, $guildTag = false, $parseColors = true) {
        $rank = $this->getRank(false);
        $out = $rank->getColor() . $this->getName();
        if ($prefix) {
            $out = ($this->getPrefix() != null ? $this->getPrefix() : $rank->getPrefix($this)) . ' ' . $this->getName();
        }
        if ($guildTag) {
            $out .= $this->getGuildTag() != null ? ' §7[' . $this->getGuildTag() . ']' : '';
        }
        if ($parseColors) {
            $outStr = Utilities::parseColors($out);
        } else {
            $outStr = Utilities::stripColors($out);
        }
        $extraKey = (($prefix ? "prefix_" : '') . ($guildTag ? 'guild_tag_' : '') . ($parseColors ? '' : 'no_color_') . 'name');
        if ($this->getExtra($extraKey, '') != $outStr) {
            $this->setExtra([$extraKey => $outStr]);
        }
        return $outStr;
    }

    /**
     * get Rank
     * @param bool $package
     * @param array $rankKeys
     * @return Rank
     */
    public function getRank($package = true, $rankKeys = ['newPackageRank', 'packageRank']) {
        $returnRank = null;
        if ($package) {
            $returnRank = null;
            foreach ($rankKeys as $key) {
                $rank = RankTypes::fromName($this->get($key));
                if ($rank != null) {
                    if ($returnRank == null) $returnRank = $rank;
                    /** @var $rank Rank */
                    /** @var $returnRank Rank */
                    if ($rank->getId() > $returnRank->getId()) {
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
     * Get player Guild Tag, null if no guild/tag
     *
     * @return string|null
     */
    public function getGuildTag() {
        $guild = $this->getGuild();
        if ($guild instanceof Guild) {
            if ($guild->canTag()) {
                return $guild->getTag();
            }
        }
        return null;
    }

    public function getRawFormattedName($prefix = true, $guildTag = false) {
        $rank = $this->getRank(false);
        $out = $rank->getColor() . $this->getName();
        if ($prefix) {
            $out = ($this->getPrefix() != null ? $this->getPrefix() : $rank->getPrefix($this)) . ' ' . $this->getName();
        }
        if ($guildTag) {
            $out .= $this->getGuildTag() != null ? ' §7[' . $this->getGuildTag() . ']' : '';
        }
        $extraKey = (($prefix ? "prefix_" : '') . ($guildTag ? 'guild_tag_' : '') . '_raw_name');
        if ($this->getExtra($extraKey, '') != $out) {
            $this->setExtra([$extraKey => $out]);
        }
        return $out;
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
     * Get Level of player
     *
     * @param bool $zeroBased
     * @return int
     */
    public function getLevel($zeroBased = false) {
        return $this->getInt('networkLevel') + (!$zeroBased ? 1 : 0);
    }

    function getCacheTimeKey() {
        return CacheTimes::PLAYER;
    }

}