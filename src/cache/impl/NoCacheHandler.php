<?php

namespace Plancke\HypixelPHP\cache\impl;

use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\gameCounts\GameCounts;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\PlayerCount;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\responses\skyblock\SkyBlockAuctions;
use Plancke\HypixelPHP\responses\skyblock\SkyBlockProfile;
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

    function setPlayer(Player $player) {
    }

    function getPlayer($uuid) {
        return null;
    }

    function setPlayerUUID($username, $uuid) {
    }

    function getUUID($username) {
        return null;
    }

    function setGuild(Guild $guild) {
    }

    function getGuild($id) {
        return null;
    }

    function setGuildIDForUUID($uuid, $id) {
    }

    function getGuildIDForUUID($uuid) {
        return null;
    }

    function setGuildIDForName($name, $id) {
    }

    function getGuildIDForName($name) {
        return null;
    }

    function setFriends(Friends $friends) {
    }

    function getFriends($uuid) {
        return null;
    }

    function setSession(Session $session) {
    }

    function getSession($uuid) {
        return null;
    }

    function setKeyInfo(KeyInfo $keyInfo) {
    }

    function getKeyInfo($key) {
        return null;
    }

    function setLeaderboards(Leaderboards $leaderboards) {
    }

    function getLeaderboards() {
        return null;
    }

    function setBoosters(Boosters $boosters) {
    }

    function getBoosters() {
        return null;
    }

    function setWatchdogStats(WatchdogStats $watchdogStats) {
    }

    function getWatchdogStats() {
        return null;
    }

    function setPlayerCount(PlayerCount $playerCount) {
    }

    function getPlayerCount() {
        return null;
    }

    function setGameCounts(GameCounts $gameCounts) {
    }

    function getGameCounts() {
        return null;
    }

    public function getSkyBlockProfile($profile_id) {
        return null;
    }

    public function setSkyBlockProfile(SkyBlockProfile $profile) {
    }

    public function getResource($resource) {
        return null;
    }

    public function setResource($resource) {
    }
}