<?php

namespace Plancke\HypixelPHP\cache;

use Closure;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\classes\Module;
use Plancke\HypixelPHP\exceptions\ExceptionCodes;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\responses\WatchdogStats;

abstract class CacheHandler extends Module {

    // cache time to only get cache or null if not present
    const MAX_CACHE_TIME = 999999999999;
    // cache time to only fetch if we don't have cached data
    const MAX_CACHE_TIME_GET_NON_EXIST = CacheHandler::MAX_CACHE_TIME - 1;

    protected $cacheTimes = [
        CacheTimes::PLAYER => 10 * 60,
        CacheTimes::UUID => 6 * 60 * 60,
        CacheTimes::UUID_NOT_FOUND => 2 * 60 * 60,

        CacheTimes::GUILD => 15 * 60,
        CacheTimes::GUILD_NOT_FOUND => 10 * 60,

        CacheTimes::LEADERBOARDS => 10 * 60,
        CacheTimes::PLAYER_COUNT => 10 * 60,
        CacheTimes::BOOSTERS => 10 * 60,
        CacheTimes::SESSION => 10 * 60,
        CacheTimes::KEY_INFO => 10 * 60,
        CacheTimes::FRIENDS => 10 * 60,
        CacheTimes::WATCHDOG => 10 * 60
    ];

    protected $globalTime = 0;

    public function canFetch() {
        return $this->globalTime != CacheHandler::MAX_CACHE_TIME;
    }

    /**
     * @return int
     */
    public function getGlobalTime() {
        return $this->globalTime;
    }

    /**
     * @param int $globalTime
     */
    public function setGlobalTime($globalTime) {
        $this->globalTime = $globalTime;
    }

    /**
     * @return array
     */
    public function getCacheTimes() {
        return $this->cacheTimes;
    }

    /**
     * Returns the currently set cache time
     * @param $for
     * @return int
     */
    public function getCacheTime($for) {
        if (isset($this->cacheTimes[$for])) {
            return max($this->globalTime, $this->cacheTimes[$for]);
        }
        return $this->globalTime;
    }

    /**
     * @param string $for
     * @param int $int
     * @return $this
     */
    public function setCacheTime($for, $int) {
        $this->cacheTimes[$for] = $int;
        return $this;
    }

    /**
     * @param HypixelObject $hypixelObject
     * @throws HypixelPHPException
     */
    public function _setCache($hypixelObject) {
        if ($hypixelObject instanceof Player) {
            $this->setCachedPlayer($hypixelObject);
        } elseif ($hypixelObject instanceof Guild) {
            $this->setCachedGuild($hypixelObject);
        } elseif ($hypixelObject instanceof Friends) {
            $this->setCachedFriends($hypixelObject);
        } elseif ($hypixelObject instanceof Session) {
            $this->setCachedSession($hypixelObject);
        } elseif ($hypixelObject instanceof KeyInfo) {
            $this->setCachedKeyInfo($hypixelObject);
        } elseif ($hypixelObject instanceof Leaderboards) {
            $this->setCachedLeaderboards($hypixelObject);
        } elseif ($hypixelObject instanceof Boosters) {
            $this->setCachedBoosters($hypixelObject);
        } elseif ($hypixelObject instanceof WatchdogStats) {
            $this->setCachedWatchdogStats($hypixelObject);
        } else {
            throw new HypixelPHPException("Invalid HypixelObject", ExceptionCodes::INVALID_HYPIXEL_OBJECT);
        }
    }

    protected function objToArray($obj) {
        if ($obj instanceof HypixelObject) {
            return $obj->getRaw();
        }
        return $obj;
    }

    protected function wrapProvider(Closure $provider, $data) {
        if ($data == null) {
            return null;
        }
        return $provider($this->getHypixelPHP(), $data);
    }

    abstract function setCachedPlayer(Player $player);

    /**
     * @param $uuid
     * @return Player
     */
    abstract function getCachedPlayer($uuid);

    abstract function setPlayerUUID($username, $obj);

    abstract function getUUID($username);

    abstract function setCachedGuild(Guild $guild);

    /**
     * @param $id
     * @return Guild
     */
    abstract function getCachedGuild($id);

    abstract function setGuildIDForUUID($uuid, $obj);

    abstract function getGuildIDForUUID($uuid);

    abstract function setGuildIDForName($name, $obj);

    abstract function getGuildIDForName($name);

    abstract function setCachedFriends(Friends $friends);

    /**
     * @param $uuid
     * @return Friends
     */
    abstract function getCachedFriends($uuid);

    abstract function setCachedSession(Session $session);

    /**
     * @param $uuid
     * @return Session
     */
    abstract function getCachedSession($uuid);

    abstract function setCachedKeyInfo(KeyInfo $keyInfo);

    /**
     * @param $key
     * @return KeyInfo
     */
    abstract function getCachedKeyInfo($key);

    abstract function setCachedLeaderboards(Leaderboards $leaderboards);

    /**
     * @return Leaderboards
     */
    abstract function getCachedLeaderboards();

    abstract function setCachedBoosters(Boosters $boosters);

    /**
     * @return Boosters
     */
    abstract function getCachedBoosters();

    abstract function setCachedWatchdogStats(WatchdogStats $watchdogStats);

    /**
     * @return WatchdogStats
     */
    abstract function getCachedWatchdogStats();
}