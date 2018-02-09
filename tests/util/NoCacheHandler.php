<?php

namespace Plancke\Tests\util;

use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\fetch\Response;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\gameCounts\GameCounts;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\PlayerCount;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\responses\WatchdogStats;

/**
 * Class NoCacheHandler
 * @package Plancke\Tests\util
 *
 * CacheHandler implementation that always returns null and never stores.
 * Useful for testing since you'll always get fresh results.
 */
class NoCacheHandler extends CacheHandler {

    /**
     * @param Player $player
     * @return void
     */
    function setCachedPlayer(Player $player) {
    }

    /**
     * @param $uuid
     * @return null|Response|Player
     */
    function getCachedPlayer($uuid) {
        return null;
    }

    /**
     * @param $username
     * @param $uuid
     * @return void
     */
    function setPlayerUUID($username, $uuid) {
    }

    /**
     * @param $username
     * @return string
     */
    function getUUID($username) {
        return null;
    }

    /**
     * @param Guild $guild
     * @return void
     */
    function setCachedGuild(Guild $guild) {
    }

    /**
     * @param $id
     * @return null|Response|Guild
     */
    function getCachedGuild($id) {
        return null;
    }

    /**
     * @param $uuid
     * @param $id
     * @return void
     */
    function setGuildIDForUUID($uuid, $id) {
    }

    /**
     * @param $uuid
     * @return mixed
     */
    function getGuildIDForUUID($uuid) {
        return null;
    }

    /**
     * @param $name
     * @param $id
     * @return void
     */
    function setGuildIDForName($name, $id) {
    }

    /**
     * @param $name
     * @return mixed
     */
    function getGuildIDForName($name) {
        return null;
    }

    /**
     * @param Friends $friends
     * @return void
     */
    function setCachedFriends(Friends $friends) {
    }

    /**
     * @param $uuid
     * @return null|Response|Friends
     */
    function getCachedFriends($uuid) {
        return null;
    }

    /**
     * @param Session $session
     * @return void
     */
    function setCachedSession(Session $session) {
    }

    /**
     * @param $uuid
     * @return null|Response|Session
     */
    function getCachedSession($uuid) {
        return null;
    }

    /**
     * @param KeyInfo $keyInfo
     * @return void
     */
    function setCachedKeyInfo(KeyInfo $keyInfo) {
    }

    /**
     * @param $key
     * @return null|Response|KeyInfo
     */
    function getCachedKeyInfo($key) {
        return null;
    }

    /**
     * @param Leaderboards $leaderboards
     * @return void
     */
    function setCachedLeaderboards(Leaderboards $leaderboards) {
    }

    /**
     * @return null|Response|Leaderboards
     */
    function getCachedLeaderboards() {
        return null;
    }

    /**
     * @param Boosters $boosters
     * @return void
     */
    function setCachedBoosters(Boosters $boosters) {
    }

    /**
     * @return null|Response|Boosters
     */
    function getCachedBoosters() {
        return null;
    }

    /**
     * @param WatchdogStats $watchdogStats
     * @return void
     */
    function setCachedWatchdogStats(WatchdogStats $watchdogStats) {
    }

    /**
     * @return null|Response|WatchdogStats
     */
    function getCachedWatchdogStats() {
        return null;
    }

    /**
     * @param PlayerCount $playerCount
     * @return void
     */
    function setCachedPlayerCount(PlayerCount $playerCount) {
    }

    /**
     * @return null|Response|PlayerCount
     */
    function getCachedPlayerCount() {
        return null;
    }

    /**
     * @param GameCounts $gameCounts
     * @return void
     */
    function setCachedGameCounts(GameCounts $gameCounts) {
    }

    /**
     * @return null|Response|GameCounts
     */
    function getCachedGameCounts() {
        return null;
    }
}