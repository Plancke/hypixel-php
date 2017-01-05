<?php

namespace Plancke\HypixelPHP\cache\impl;

use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\cache\CacheTypes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\responses\WatchdogStats;
use Plancke\HypixelPHP\util\CacheUtil;
use Plancke\HypixelPHP\util\Utilities;

/**
 * Implementation for CacheHandler, stores data flat filed
 *
 * Class FlatFileCacheHandler
 * @package HypixelPHP
 */
class FlatFileCacheHandler extends CacheHandler {

    protected $baseDirectory = "cache" . DIRECTORY_SEPARATOR . "HypixelPHP" . DIRECTORY_SEPARATOR;

    /**
     * @return string
     */
    public function getBaseDirectory() {
        return $this->baseDirectory;
    }

    /**
     * @param string $baseDirectory
     * @return $this
     */
    public function setBaseDirectory($baseDirectory) {
        $this->baseDirectory = $baseDirectory;
        return $this;
    }

    /**
     * @param $filename
     * @param HypixelObject $obj
     */
    protected function setObjCache($filename, HypixelObject $obj) {
        $this->setCache($filename, $this->objToArray($obj));
    }

    /**
     * @param $filename
     * @param $obj
     */
    protected function setCache($filename, $obj) {
        Utilities::setFileContent($this->baseDirectory . DIRECTORY_SEPARATOR . $filename . '.json', json_encode($obj));
    }

    /**
     * @param $filename
     * @return array|null
     */
    protected function getCache($filename) {
        $content = Utilities::getFileContent($this->baseDirectory . DIRECTORY_SEPARATOR . $filename . '.json');
        if ($content == null) {
            return null;
        }
        return json_decode($content, true);
    }

    function setCachedPlayer(Player $player) {
        $this->setObjCache(CacheTypes::PLAYERS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($player->getUUID()), $player);
    }

    function getCachedPlayer($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getPlayer(),
            $data = $this->getCache(CacheTypes::PLAYERS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid))
        );
    }

    function setPlayerUUID($username, $obj) {
        $this->setCache(CacheTypes::PLAYER_UUID . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($username), $obj);
    }

    function getUUID($username) {
        $data = $this->getCache(CacheTypes::PLAYER_UUID . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($username));
        if ($data != null) {
            if (isset($data['uuid']) && $data['uuid'] != null && $data['uuid'] != '') {
                $cacheTime = $this->getCacheTime(CacheTimes::UUID);
            } else {
                $cacheTime = $this->getCacheTime(CacheTimes::UUID_NOT_FOUND);
            }
            $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;
            $diff = time() - $cacheTime - $timestamp;

            $this->getHypixelPHP()->getLogger()->log("Found name match in PLAYER_UUID! '$diff'");

            if ($diff < 0) {
                return $data['uuid'];
            }
        }

        return null;
    }

    function setCachedGuild(Guild $guild) {
        $this->setObjCache(CacheTypes::GUILDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($guild->getID()), $guild);
    }

    function getCachedGuild($id) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getGuild(),
            $data = $this->getCache(CacheTypes::GUILDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($id))
        );
    }

    function setGuildIDForUUID($uuid, $obj) {
        $this->setCache(CacheTypes::GUILDS_UUID . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid), $obj);
    }

    function getGuildIDForUUID($uuid) {
        return $this->getCache(CacheTypes::GUILDS_UUID . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid));
    }

    function setGuildIDForName($name, $obj) {
        $this->setCache(CacheTypes::GUILDS_NAME . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($name), $obj);
    }

    function getGuildIDForName($name) {
        return $this->getCache(CacheTypes::GUILDS_NAME . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($name));
    }

    function setCachedFriends(Friends $friends) {
        $this->setObjCache(CacheTypes::FRIENDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($friends->getUUID()), $friends);
    }

    function getCachedFriends($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getFriends(),
            $this->getCache(CacheTypes::FRIENDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid))
        );
    }

    function setCachedSession(Session $session) {
        $this->setObjCache(CacheTypes::SESSIONS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($session->getUUID()), $session);
    }

    function getCachedSession($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getSession(),
            $this->getCache(CacheTypes::SESSIONS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid))
        );
    }

    function setCachedKeyInfo(KeyInfo $keyInfo) {
        $this->setObjCache(CacheTypes::API_KEYS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($keyInfo->getKey()), $keyInfo);
    }

    function getCachedKeyInfo($key) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getKeyInfo(),
            $this->getCache(CacheTypes::SESSIONS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($key))
        );
    }

    function setCachedLeaderboards(Leaderboards $leaderboards) {
        $this->setObjCache(CacheTypes::LEADERBOARDS, $leaderboards);
    }

    function getCachedLeaderboards() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getLeaderboards(),
            $this->getCache(CacheTypes::LEADERBOARDS)
        );
    }

    function setCachedBoosters(Boosters $boosters) {
        $this->setObjCache(CacheTypes::BOOSTERS, $boosters);
    }

    function getCachedBoosters() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getBoosters(),
            $this->getCache(CacheTypes::BOOSTERS)
        );
    }

    function setCachedWatchdogStats(WatchdogStats $watchdogStats) {
        $this->setObjCache(CacheTypes::WATCHDOG_STATS, $watchdogStats);
    }

    function getCachedWatchdogStats() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getWatchdogStats(),
            $this->getCache(CacheTypes::WATCHDOG_STATS)
        );
    }
}