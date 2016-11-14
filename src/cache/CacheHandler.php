<?php

namespace Plancke\HypixelPHP\cache;

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

    protected $cacheTimes = [];
    protected $globalTime;

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
    function _setCache($hypixelObject) {
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
        } else {
            throw new HypixelPHPException("Invalid HypixelObject", ExceptionCodes::INVALID_HYPIXEL_OBJECT);
        }
    }

    //@formatter:off
    abstract function setCachedPlayer(Player $player);
    abstract function getCachedPlayer($uuid);
    abstract function setPlayerUUID($username, $obj);
    abstract function getUUID($username);

    abstract function setCachedGuild(Guild $guild);
    abstract function getCachedGuild($id);
    abstract function setGuildIDForUUID($uuid, $obj);
    abstract function getGuildIDForUUID($uuid);
    abstract function setGuildIDForName($name, $obj);
    abstract function getGuildIDForName($name);

    abstract function setCachedFriends(Friends $friends);
    abstract function getCachedFriends($uuid);

    abstract function setCachedSession(Session $session);
    abstract function getCachedSession($uuid);

    abstract function setCachedKeyInfo(KeyInfo $keyInfo);
    abstract function getCachedKeyInfo($key);

    abstract function setCachedLeaderboards(Leaderboards $leaderboards);
    abstract function getCachedLeaderboards();

    abstract function setCachedBoosters(Boosters $boosters);
    abstract function getCachedBoosters();

    abstract function setCachedWatchdogStats(WatchdogStats $watchdogStats);
    abstract function getCachedWatchdogStats();
    //@formatter:on
}