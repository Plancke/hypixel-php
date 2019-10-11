<?php

namespace Plancke\HypixelPHP\cache\impl;

use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\cache\CacheTypes;
use Plancke\HypixelPHP\exceptions\InvalidArgumentException;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\gameCounts\GameCounts;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\PlayerCount;
use Plancke\HypixelPHP\responses\Resource;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\responses\skyblock\SkyBlockProfile;
use Plancke\HypixelPHP\responses\WatchdogStats;
use Plancke\HypixelPHP\util\CacheUtil;

/**
 * Implementation for CacheHandler, stores data flat file
 *
 * Class FlatFileCacheHandler
 * @package HypixelPHP
 */
class FlatFileCacheHandler extends CacheHandler {

    protected $baseDirectory = __DIR__ . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "HypixelPHP" . DIRECTORY_SEPARATOR;

    /**
     * Get base file location for all cache files
     *
     * @return string
     */
    public function getBaseDirectory() {
        return $this->baseDirectory;
    }

    /**
     * Modify the base directory for all cache files
     *
     * @param string $baseDirectory
     * @return $this
     */
    public function setBaseDirectory($baseDirectory) {
        $this->baseDirectory = $baseDirectory;
        return $this;
    }

    /**
     * @param $uuid
     * @return Player|null
     */
    public function getPlayer($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getPlayer(),
            $data = $this->_getCache(CacheTypes::PLAYERS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid))
        );
    }

    /**
     * @param $filename
     * @return array|null
     */
    protected function _getCache($filename) {
        $filename = $this->baseDirectory . DIRECTORY_SEPARATOR . $filename . '.json';
        if (!file_exists($filename)) return null;

        $content = file_get_contents($filename);
        if ($content == null) return null;

        return json_decode($content, true);
    }

    /**
     * @param Player $player
     * @throws InvalidArgumentException
     */
    public function setPlayer(Player $player) {
        $this->_setCache(CacheTypes::PLAYERS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($player->getUUID()), $player);
    }

    /**
     * Save given array to file
     *
     * @param $filename
     * @param $obj
     * @throws InvalidArgumentException
     */
    protected function _setCache($filename, $obj) {
        $filename = $this->baseDirectory . DIRECTORY_SEPARATOR . $filename . '.json';
        $content = json_encode($this->objToArray($obj));

        if (!file_exists(dirname($filename))) {
            // create directory
            @mkdir(dirname($filename), 0744, true);
        }
        file_put_contents($filename, $content);
    }

    /**
     * @param $username
     * @return string|null
     */
    public function getUUID($username) {
        $data = $this->_getCache(CacheTypes::PLAYER_UUID . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($username));
        if ($data != null) {
            if (isset($data['uuid']) && $data['uuid'] != null && $data['uuid'] != '') {
                $cacheTime = $this->getCacheTime(CacheTimes::UUID);
            } else {
                $cacheTime = $this->getCacheTime(CacheTimes::UUID_NOT_FOUND);
            }
            $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;
            $diff = time() - $cacheTime - $timestamp;

            $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, "Found name match in PLAYER_UUID! '$diff'");

            if ($diff < 0) {
                return $data['uuid'];
            }
        }

        return null;
    }

    /**
     * @param $username
     * @param $obj
     * @throws InvalidArgumentException
     */
    public function setPlayerUUID($username, $obj) {
        $this->_setCache(CacheTypes::PLAYER_UUID . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($username), $obj);
    }

    /**
     * @param $id
     * @return Guild|null
     */
    public function getGuild($id) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getGuild(),
            $data = $this->_getCache(CacheTypes::GUILDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($id))
        );
    }

    /**
     * @param Guild $guild
     * @throws InvalidArgumentException
     */
    public function setGuild(Guild $guild) {
        $this->_setCache(CacheTypes::GUILDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($guild->getID()), $guild);
    }

    /**
     * @param $uuid
     * @return string|null
     */
    public function getGuildIDForUUID($uuid) {
        $cached = $this->_getCache(CacheTypes::GUILDS_UUID . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid));
        if ($cached == null) return null;

        if (isset($cached['uuid']) && $cached['uuid'] != null && $cached['uuid'] != '') {
            $cacheTime = $this->getCacheTime(CacheTimes::GUILD);
        } else {
            $cacheTime = $this->getCacheTime(CacheTimes::GUILD_NOT_FOUND);
        }
        $timestamp = array_key_exists('timestamp', $cached) ? $cached['timestamp'] : 0;
        if (CacheUtil::isExpired($timestamp, $cacheTime)) return null;
        return $cached['guild'];
    }

    /**
     * @param $uuid
     * @param $obj
     * @throws InvalidArgumentException
     */
    public function setGuildIDForUUID($uuid, $obj) {
        $this->_setCache(CacheTypes::GUILDS_UUID . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid), $obj);
    }

    /**
     * @param $name
     * @return string|null
     */
    public function getGuildIDForName($name) {
        $cached = $this->_getCache(CacheTypes::GUILDS_NAME . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($name));
        if ($cached == null) return null;

        if (isset($cached['name_lower']) && $cached['name_lower'] != null && $cached['name_lower'] != '') {
            $cacheTime = $this->getCacheTime(CacheTimes::GUILD);
        } else {
            $cacheTime = $this->getCacheTime(CacheTimes::GUILD_NOT_FOUND);
        }

        $timestamp = array_key_exists('timestamp', $cached) ? $cached['timestamp'] : 0;
        if (CacheUtil::isExpired($timestamp, $cacheTime)) return null;
        return $cached['guild'];
    }

    /**
     * @param $name
     * @param $obj
     * @throws InvalidArgumentException
     */
    public function setGuildIDForName($name, $obj) {
        $this->_setCache(CacheTypes::GUILDS_NAME . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($name), $obj);
    }

    /**
     * @param $uuid
     * @return Friends|null
     */
    public function getFriends($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getFriends(),
            $this->_getCache(CacheTypes::FRIENDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid))
        );
    }

    /**
     * @param Friends $friends
     * @throws InvalidArgumentException
     */
    public function setFriends(Friends $friends) {
        $this->_setCache(CacheTypes::FRIENDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($friends->getUUID()), $friends);
    }

    /**
     * @param $uuid
     * @return Session|null
     */
    public function getSession($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getSession(),
            $this->_getCache(CacheTypes::SESSIONS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid))
        );
    }

    /**
     * @param Session $session
     * @throws InvalidArgumentException
     */
    public function setSession(Session $session) {
        $this->_setCache(CacheTypes::SESSIONS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($session->getUUID()), $session);
    }

    /**
     * @param $key
     * @return KeyInfo|null
     */
    public function getKeyInfo($key) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getKeyInfo(),
            $this->_getCache(CacheTypes::SESSIONS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($key))
        );
    }

    /**
     * @param KeyInfo $keyInfo
     * @throws InvalidArgumentException
     */
    public function setKeyInfo(KeyInfo $keyInfo) {
        $this->_setCache(CacheTypes::API_KEYS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($keyInfo->getKey()), $keyInfo);
    }

    /**
     * @return Leaderboards|null
     */
    public function getLeaderboards() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getLeaderboards(),
            $this->_getCache(CacheTypes::LEADERBOARDS)
        );
    }

    /**
     * @param Leaderboards $leaderboards
     * @throws InvalidArgumentException
     */
    public function setLeaderboards(Leaderboards $leaderboards) {
        $this->_setCache(CacheTypes::LEADERBOARDS, $leaderboards);
    }

    /**
     * @return Boosters|null
     */
    public function getBoosters() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getBoosters(),
            $this->_getCache(CacheTypes::BOOSTERS)
        );
    }

    /**
     * @param Boosters $boosters
     * @throws InvalidArgumentException
     */
    public function setBoosters(Boosters $boosters) {
        $this->_setCache(CacheTypes::BOOSTERS, $boosters);
    }

    /**
     * @return WatchdogStats|null
     */
    public function getWatchdogStats() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getWatchdogStats(),
            $this->_getCache(CacheTypes::WATCHDOG_STATS)
        );
    }

    /**
     * @param WatchdogStats $watchdogStats
     * @throws InvalidArgumentException
     */
    public function setWatchdogStats(WatchdogStats $watchdogStats) {
        $this->_setCache(CacheTypes::WATCHDOG_STATS, $watchdogStats);
    }

    /**
     * @return PlayerCount|null
     */
    public function getPlayerCount() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getPlayerCount(),
            $this->_getCache(CacheTypes::PLAYER_COUNT)
        );
    }

    /**
     * @param PlayerCount $playerCount
     * @throws InvalidArgumentException
     */
    public function setPlayerCount(PlayerCount $playerCount) {
        $this->_setCache(CacheTypes::PLAYER_COUNT, $playerCount);
    }

    /**
     * @return GameCounts|null
     */
    public function getGameCounts() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getGameCounts(),
            $this->_getCache(CacheTypes::GAME_COUNTS)
        );
    }

    /**
     * @param GameCounts $gameCounts
     * @throws InvalidArgumentException
     */
    public function setGameCounts(GameCounts $gameCounts) {
        $this->_setCache(CacheTypes::GAME_COUNTS, $gameCounts);
    }

    /**
     * @param $profile_id
     * @return SkyBlockProfile|null
     */
    public function getSkyBlockProfile($profile_id) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getSkyBlockProfile(),
            $this->_getCache(CacheTypes::SKYBLOCK_PROFILES . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($profile_id))
        );
    }

    /**
     * @param SkyBlockProfile $profile
     * @return void
     * @throws InvalidArgumentException
     */
    public function setSkyBlockProfile(SkyBlockProfile $profile) {
        $this->_setCache(CacheTypes::SKYBLOCK_PROFILES . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($profile->getProfileId()), $profile);
    }

    /**
     * @param $resource
     * @return Resource|null
     */
    public function getResource($resource) {
        return $this->wrapProvider(
            function ($HypixelPHP, $data) use ($resource) {
                return new Resource($HypixelPHP, $data, $resource);
            },
            $this->_getCache(CacheTypes::RESOURCES . DIRECTORY_SEPARATOR . $resource)
        );
    }

    /**
     * @param Resource $resource
     * @return void
     * @throws InvalidArgumentException
     */
    public function setResource($resource) {
        $this->_setCache(CacheTypes::RESOURCES . DIRECTORY_SEPARATOR . $resource->getResource(), $resource);
    }

}