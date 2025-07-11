<?php

namespace Plancke\HypixelPHP\cache\impl;

use InvalidArgumentException;
use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\cache\CacheTypes;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\counts\Counts;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\PunishmentStats;
use Plancke\HypixelPHP\responses\RecentGames;
use Plancke\HypixelPHP\responses\Resource;
use Plancke\HypixelPHP\responses\skyblock\SkyBlockProfile;
use Plancke\HypixelPHP\responses\Status;
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
     * @param $uuid
     * @return Guild|null
     */
    public function getGuildByPlayer($uuid) {
        throw new InvalidArgumentException("getGuildByPlayer is not implemented in FlatFileCacheHandler");
    }

    /**
     * @param $name
     * @return Guild|null
     */
    public function getGuildByName($name) {
        throw new InvalidArgumentException("getGuildByName is not implemented in FlatFileCacheHandler");
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
     * @return Status|null
     */
    public function getStatus($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getStatus(),
            $this->_getCache(CacheTypes::STATUS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid))
        );
    }

    /**
     * @param Status $status
     * @throws InvalidArgumentException
     */
    public function setStatus(Status $status) {
        $this->_setCache(CacheTypes::STATUS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($status->getUUID()), $status);
    }

    /**
     * @param $uuid
     * @return RecentGames|null
     */
    public function getRecentGames($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getRecentGames(),
            $this->_getCache(CacheTypes::RECENT_GAMES . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid))
        );
    }

    /**
     * @param RecentGames $recentGames
     * @throws InvalidArgumentException
     */
    public function setRecentGames(RecentGames $recentGames) {
        $this->_setCache(CacheTypes::STATUS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($recentGames->getUUID()), $recentGames);
    }

    /**
     * @param $key
     * @return KeyInfo|null
     */
    public function getKeyInfo($key) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getKeyInfo(),
            $this->_getCache(CacheTypes::STATUS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($key))
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
     * @return PunishmentStats|null
     */
    public function getPunishmentStats() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getPunishmentStats(),
            $this->_getCache(CacheTypes::PUNISHMENT_STATS)
        );
    }

    /**
     * @param PunishmentStats $punishmentStats
     * @throws InvalidArgumentException
     */
    public function setPunishmentStats(PunishmentStats $punishmentStats) {
        $this->_setCache(CacheTypes::PUNISHMENT_STATS, $punishmentStats);
    }

    /**
     * @return Counts|null
     */
    public function getCounts() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getCounts(),
            $this->_getCache(CacheTypes::COUNTS)
        );
    }

    /**
     * @param Counts $counts
     * @throws InvalidArgumentException
     */
    public function setCounts(Counts $counts) {
        $this->_setCache(CacheTypes::COUNTS, $counts);
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