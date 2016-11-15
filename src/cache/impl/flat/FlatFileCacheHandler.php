<?php

namespace Plancke\HypixelPHP\cache\impl\flat;

use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\responses\WatchdogStats;
use Plancke\HypixelPHP\util\Utilities;

/**
 * Implementation for CacheHandler, stores data flat filed
 *
 * Class FlatFileCacheHandler
 * @package HypixelPHP
 */
class FlatFileCacheHandler extends CacheHandler {

    /**
     * @param               $filename
     * @param HypixelObject $obj
     */
    public function setCache($filename, HypixelObject $obj) {
        $content = json_encode($obj->getRaw());
        Utilities::setFileContent($filename, $content);
    }

    /**
     * @param $filename
     * @return array|null
     */
    public function getCache($filename) {
        $content = Utilities::getFileContent($filename);
        if ($content == null) {
            return null;
        }
        return json_decode($content, true);
    }

    function setCachedPlayer(Player $player) {
        // TODO: Implement setCachedPlayer() method.
    }

    function getCachedPlayer($uuid) {
        // TODO: Implement getCachedPlayer() method.
    }

    function setPlayerUUID($username, $obj) {
        // TODO: Implement setPlayerUUID() method.
    }

    function getUUID($username) {
        // TODO: Implement getUUID() method.
    }

    function setCachedGuild(Guild $guild) {
        // TODO: Implement setCachedGuild() method.
    }

    function getCachedGuild($id) {
        // TODO: Implement getCachedGuild() method.
    }

    function setGuildIDForUUID($uuid, $obj) {
        // TODO: Implement setGuildIDForUUID() method.
    }

    function getGuildIDForUUID($uuid) {
        // TODO: Implement getGuildIDForUUID() method.
    }

    function setGuildIDForName($name, $obj) {
        // TODO: Implement setGuildIDForName() method.
    }

    function getGuildIDForName($name) {
        // TODO: Implement getGuildIDForName() method.
    }

    function setCachedFriends(Friends $friends) {
        // TODO: Implement setCachedFriends() method.
    }

    function getCachedFriends($uuid) {
        // TODO: Implement getCachedFriends() method.
    }

    function setCachedSession(Session $session) {
        // TODO: Implement setCachedSession() method.
    }

    function getCachedSession($uuid) {
        // TODO: Implement getCachedSession() method.
    }

    function setCachedKeyInfo(KeyInfo $keyInfo) {
        // TODO: Implement setCachedKeyInfo() method.
    }

    function getCachedKeyInfo($key) {
        // TODO: Implement getCachedKeyInfo() method.
    }

    function setCachedLeaderboards(Leaderboards $leaderboards) {
        // TODO: Implement setCachedLeaderboards() method.
    }

    function getCachedLeaderboards() {
        // TODO: Implement getCachedLeaderboards() method.
    }

    function setCachedBoosters(Boosters $boosters) {
        // TODO: Implement setCachedBoosters() method.
    }

    function getCachedBoosters() {
        // TODO: Implement getCachedBoosters() method.
    }

    function setCachedWatchdogStats(WatchdogStats $watchdogStats) {
        // TODO: Implement setCachedWatchdogStats() method.
    }

    function getCachedWatchdogStats() {
        // TODO: Implement getCachedWatchdogStats() method.
    }
}