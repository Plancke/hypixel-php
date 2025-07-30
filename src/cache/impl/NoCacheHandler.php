<?php

namespace Plancke\HypixelPHP\cache\impl;

use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\counts\Counts;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\PunishmentStats;
use Plancke\HypixelPHP\responses\RecentGames;
use Plancke\HypixelPHP\responses\skyblock\SkyBlockProfile;
use Plancke\HypixelPHP\responses\Status;

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

    public function getGuildByPlayer($uuid) {
        return null;
    }

    public function getGuildByName($name) {
        return null;
    }

    function setStatus(Status $status) {
    }

    function getStatus($uuid) {
        return null;
    }

    public function getRecentGames($uuid) {
        return null;
    }

    public function setRecentGames(RecentGames $recentGames) {
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

    function setPunishmentStats(PunishmentStats $punishmentStats) {
    }

    function getPunishmentStats() {
        return null;
    }

    function setCounts(Counts $counts) {
    }

    function getCounts() {
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