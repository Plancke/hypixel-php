<?php

namespace Plancke\HypixelPHP\cache\impl;

use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\cache\CacheTypes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\exceptions\InvalidArgumentException;
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
use Plancke\HypixelPHP\util\CacheUtil;
use Plancke\HypixelPHP\util\Utilities;

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
     * @param Player $player
     * @throws InvalidArgumentException
     */
    function setCachedPlayer(Player $player) {
        $this->setObjCache(CacheTypes::PLAYERS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($player->getUUID()), $player);
    }

    /**
     * Save {@link HypixelObject} to file.
     *
     * @param $filename
     * @param HypixelObject $obj
     * @throws InvalidArgumentException
     */
    protected function setObjCache($filename, HypixelObject $obj) {
        $this->setCache($filename, $this->objToArray($obj));
    }

    /**
     * Save given array to file
     *
     * @param $filename
     * @param array $obj
     * @throws InvalidArgumentException
     */
    protected function setCache($filename, $obj) {
        Utilities::setFileContent($this->baseDirectory . DIRECTORY_SEPARATOR . $filename . '.json', json_encode($this->objToArray($obj)));
    }

    /**
     * @param $uuid
     * @return null|Response|Player
     */
    function getCachedPlayer($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getPlayer(),
            $data = $this->getCache(CacheTypes::PLAYERS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid))
        );
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

    /**
     * @param $username
     * @param $obj
     * @throws InvalidArgumentException
     */
    function setPlayerUUID($username, $obj) {
        $this->setCache(CacheTypes::PLAYER_UUID . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($username), $obj);
    }

    /**
     * @param $username
     * @return mixed|null|string
     */
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

    /**
     * @param Guild $guild
     * @throws InvalidArgumentException
     */
    function setCachedGuild(Guild $guild) {
        $this->setObjCache(CacheTypes::GUILDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($guild->getID()), $guild);
    }

    /**
     * @param $id
     * @return null|Response|Guild
     */
    function getCachedGuild($id) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getGuild(),
            $data = $this->getCache(CacheTypes::GUILDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($id))
        );
    }

    /**
     * @param $uuid
     * @param $obj
     * @throws InvalidArgumentException
     */
    function setGuildIDForUUID($uuid, $obj) {
        $this->setCache(CacheTypes::GUILDS_UUID . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid), $obj);
    }

    /**
     * @param $uuid
     * @return array|mixed|null
     */
    function getGuildIDForUUID($uuid) {
        return $this->getCache(CacheTypes::GUILDS_UUID . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid));
    }

    /**
     * @param $name
     * @param $obj
     * @throws InvalidArgumentException
     */
    function setGuildIDForName($name, $obj) {
        $this->setCache(CacheTypes::GUILDS_NAME . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($name), $obj);
    }

    /**
     * @param $name
     * @return array|mixed|null
     */
    function getGuildIDForName($name) {
        return $this->getCache(CacheTypes::GUILDS_NAME . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($name));
    }

    /**
     * @param Friends $friends
     * @throws InvalidArgumentException
     */
    function setCachedFriends(Friends $friends) {
        $this->setObjCache(CacheTypes::FRIENDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($friends->getUUID()), $friends);
    }

    /**
     * @param $uuid
     * @return null|Response|Friends
     */
    function getCachedFriends($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getFriends(),
            $this->getCache(CacheTypes::FRIENDS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid))
        );
    }

    /**
     * @param Session $session
     * @throws InvalidArgumentException
     */
    function setCachedSession(Session $session) {
        $this->setObjCache(CacheTypes::SESSIONS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($session->getUUID()), $session);
    }

    /**
     * @param $uuid
     * @return null|Response|Session
     */
    function getCachedSession($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getSession(),
            $this->getCache(CacheTypes::SESSIONS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($uuid))
        );
    }

    /**
     * @param KeyInfo $keyInfo
     * @throws InvalidArgumentException
     */
    function setCachedKeyInfo(KeyInfo $keyInfo) {
        $this->setObjCache(CacheTypes::API_KEYS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($keyInfo->getKey()), $keyInfo);
    }

    /**
     * @param $key
     * @return null|Response|KeyInfo
     */
    function getCachedKeyInfo($key) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getKeyInfo(),
            $this->getCache(CacheTypes::SESSIONS . DIRECTORY_SEPARATOR . CacheUtil::getCacheFileName($key))
        );
    }

    /**
     * @param Leaderboards $leaderboards
     * @throws InvalidArgumentException
     */
    function setCachedLeaderboards(Leaderboards $leaderboards) {
        $this->setObjCache(CacheTypes::LEADERBOARDS, $leaderboards);
    }

    /**
     * @return null|Response|Leaderboards
     */
    function getCachedLeaderboards() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getLeaderboards(),
            $this->getCache(CacheTypes::LEADERBOARDS)
        );
    }

    /**
     * @param Boosters $boosters
     * @throws InvalidArgumentException
     */
    function setCachedBoosters(Boosters $boosters) {
        $this->setObjCache(CacheTypes::BOOSTERS, $boosters);
    }

    /**
     * @return null|Response|Boosters
     */
    function getCachedBoosters() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getBoosters(),
            $this->getCache(CacheTypes::BOOSTERS)
        );
    }

    /**
     * @param WatchdogStats $watchdogStats
     * @throws InvalidArgumentException
     */
    function setCachedWatchdogStats(WatchdogStats $watchdogStats) {
        $this->setObjCache(CacheTypes::WATCHDOG_STATS, $watchdogStats);
    }

    /**
     * @return null|Response|WatchdogStats
     */
    function getCachedWatchdogStats() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getWatchdogStats(),
            $this->getCache(CacheTypes::WATCHDOG_STATS)
        );
    }

    /**
     * @param PlayerCount $playerCount
     * @throws InvalidArgumentException
     */
    function setCachedPlayerCount(PlayerCount $playerCount) {
        $this->setObjCache(CacheTypes::PLAYER_COUNT, $playerCount);
    }

    /**
     * @return null|Response|PlayerCount
     */
    function getCachedPlayerCount() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getPlayerCount(),
            $this->getCache(CacheTypes::PLAYER_COUNT)
        );
    }

    /**
     * @param GameCounts $gameCounts
     * @throws InvalidArgumentException
     */
    function setCachedGameCounts(GameCounts $gameCounts) {
        $this->setObjCache(CacheTypes::GAME_COUNTS, $gameCounts);
    }

    /**
     * @return null|Response|GameCounts
     */
    function getCachedGameCounts() {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getGameCounts(),
            $this->getCache(CacheTypes::GAME_COUNTS)
        );
    }
}