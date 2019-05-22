<?php

namespace Plancke\HypixelPHP\cache\impl;

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
 * @package Plancke\HypixelPHP\cache\impl
 *
 * CacheHandler implementation that always returns null and never stores.
 * Useful for testing since you'll always get fresh results.
 * But NOT recommended to use in production.
 */
class NoCacheHandler extends CacheHandler {

    /**
     * @param Player $player
     * @return void
     */
    function setPlayer(Player $player) {
    }

    /**
     * @param $uuid
     * @return null|Response|Player
     */
    function getPlayer($uuid) {
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
    function setGuild(Guild $guild) {
    }

    /**
     * @param $id
     * @return null|Response|Guild
     */
    function getGuild($id) {
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
    function setFriends(Friends $friends) {
    }

    /**
     * @param $uuid
     * @return null|Response|Friends
     */
    function getFriends($uuid) {
        return null;
    }

    /**
     * @param Session $session
     * @return void
     */
    function setSession(Session $session) {
    }

    /**
     * @param $uuid
     * @return null|Response|Session
     */
    function getSession($uuid) {
        return null;
    }

    /**
     * @param KeyInfo $keyInfo
     * @return void
     */
    function setKeyInfo(KeyInfo $keyInfo) {
    }

    /**
     * @param $key
     * @return null|Response|KeyInfo
     */
    function getKeyInfo($key) {
        return null;
    }

    /**
     * @param Leaderboards $leaderboards
     * @return void
     */
    function setLeaderboards(Leaderboards $leaderboards) {
    }

    /**
     * @return null|Response|Leaderboards
     */
    function getLeaderboards() {
        return null;
    }

    /**
     * @param Boosters $boosters
     * @return void
     */
    function setBoosters(Boosters $boosters) {
    }

    /**
     * @return null|Response|Boosters
     */
    function getBoosters() {
        return null;
    }

    /**
     * @param WatchdogStats $watchdogStats
     * @return void
     */
    function setWatchdogStats(WatchdogStats $watchdogStats) {
    }

    /**
     * @return null|Response|WatchdogStats
     */
    function getWatchdogStats() {
        return null;
    }

    /**
     * @param PlayerCount $playerCount
     * @return void
     */
    function setPlayerCount(PlayerCount $playerCount) {
    }

    /**
     * @return null|Response|PlayerCount
     */
    function getPlayerCount() {
        return null;
    }

    /**
     * @param GameCounts $gameCounts
     * @return void
     */
    function setGameCounts(GameCounts $gameCounts) {
    }

    /**
     * @return null|Response|GameCounts
     */
    function getGameCounts() {
        return null;
    }
}