<?php

namespace Plancke\Tests\util;

use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\responses\WatchdogStats;

class NoCacheHandler extends CacheHandler {

    function setCachedPlayer(Player $player) {
    }

    function getCachedPlayer($uuid) {
        return null;
    }

    function setPlayerUUID($username, $obj) {
    }

    function getUUID($username) {
        return null;
    }

    function setCachedGuild(Guild $guild) {
    }

    function getCachedGuild($id) {
        return null;
    }

    function setGuildIDForUUID($uuid, $obj) {
    }

    function getGuildIDForUUID($uuid) {
        return null;
    }

    function setGuildIDForName($name, $obj) {
    }

    function getGuildIDForName($name) {
        return null;
    }

    function setCachedFriends(Friends $friends) {
    }

    function getCachedFriends($uuid) {
        return null;
    }

    function setCachedSession(Session $session) {
    }

    function getCachedSession($uuid) {
        return null;
    }

    function setCachedKeyInfo(KeyInfo $keyInfo) {
    }

    function getCachedKeyInfo($key) {
        return null;
    }

    function setCachedLeaderboards(Leaderboards $leaderboards) {
    }

    function getCachedLeaderboards() {
        return null;
    }

    function setCachedBoosters(Boosters $boosters) {
    }

    function getCachedBoosters() {
        return null;
    }

    function setCachedWatchdogStats(WatchdogStats $watchdogStats) {
    }

    function getCachedWatchdogStats() {
        return null;
    }
}