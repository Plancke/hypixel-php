<?php

namespace Plancke\HypixelPHP\cache;

use Closure;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\classes\Module;
use Plancke\HypixelPHP\exceptions\ExceptionCodes;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
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

/**
 * Class CacheHandler
 * @package Plancke\HypixelPHP\cache
 */
abstract class CacheHandler extends Module {

    // cache time to only get cache or null if not present, arbitrary value
    const MAX_CACHE_TIME = 999999999999;
    // cache time to only fetch if we don't have cached data, arbitrary value
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
        CacheTimes::WATCHDOG => 10 * 60,
        CacheTimes::GAME_COUNTS => 10 * 60
    ];

    protected $globalTime = 0;

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
        } elseif ($hypixelObject instanceof PlayerCount) {
            $this->setCachedPlayerCount($hypixelObject);
        } elseif ($hypixelObject instanceof GameCounts) {
            $this->setCachedGameCounts($hypixelObject);
        } else {
            throw new HypixelPHPException("Invalid HypixelObject", ExceptionCodes::INVALID_HYPIXEL_OBJECT);
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    abstract function setCachedPlayer(Player $player);

    /**
     * @param Guild $guild
     * @return void
     */
    abstract function setCachedGuild(Guild $guild);

    /**
     * @param Friends $friends
     * @return void
     */
    abstract function setCachedFriends(Friends $friends);

    /**
     * @param Session $session
     * @return void
     */
    abstract function setCachedSession(Session $session);

    /**
     * @param KeyInfo $keyInfo
     * @return void
     */
    abstract function setCachedKeyInfo(KeyInfo $keyInfo);

    /**
     * @param Leaderboards $leaderboards
     * @return void
     */
    abstract function setCachedLeaderboards(Leaderboards $leaderboards);

    /**
     * @param Boosters $boosters
     * @return void
     */
    abstract function setCachedBoosters(Boosters $boosters);

    /**
     * @param WatchdogStats $watchdogStats
     * @return void
     */
    abstract function setCachedWatchdogStats(WatchdogStats $watchdogStats);

    /**
     * @param PlayerCount $playerCount
     * @return void
     */
    abstract function setCachedPlayerCount(PlayerCount $playerCount);

    /**
     * @param GameCounts $gameCounts
     * @return void
     */
    abstract function setCachedGameCounts(GameCounts $gameCounts);

    /**
     * @param $uuid
     * @return null|Response|Player
     */
    abstract function getCachedPlayer($uuid);

    /**
     * @param $username
     * @param $uuid
     * @return void
     */
    abstract function setPlayerUUID($username, $uuid);

    /**
     * @param $username
     * @return string
     */
    abstract function getUUID($username);

    /**
     * @param $id
     * @return null|Response|Guild
     */
    abstract function getCachedGuild($id);

    /**
     * @param $uuid
     * @param $id
     * @return void
     */
    abstract function setGuildIDForUUID($uuid, $id);

    /**
     * @param $uuid
     * @return mixed
     */
    abstract function getGuildIDForUUID($uuid);

    /**
     * @param $name
     * @param $id
     * @return void
     */
    abstract function setGuildIDForName($name, $id);

    /**
     * @param $name
     * @return mixed
     */
    abstract function getGuildIDForName($name);

    /**
     * @param $uuid
     * @return null|Response|Friends
     */
    abstract function getCachedFriends($uuid);

    /**
     * @param $uuid
     * @return null|Response|Session
     */
    abstract function getCachedSession($uuid);

    /**
     * @param $key
     * @return null|Response|KeyInfo
     */
    abstract function getCachedKeyInfo($key);

    /**
     * @return null|Response|Leaderboards
     */
    abstract function getCachedLeaderboards();

    /**
     * @return null|Response|Boosters
     */
    abstract function getCachedBoosters();

    /**
     * @return null|Response|WatchdogStats
     */
    abstract function getCachedWatchdogStats();

    /**
     * @return null|Response|PlayerCount
     */
    abstract function getCachedPlayerCount();

    /**
     * @return null|Response|GameCounts
     */
    abstract function getCachedGameCounts();

    /**
     * Convert given input to an array in order to cache it
     *
     * @param $obj
     * @return array
     * @throws InvalidArgumentException
     */
    protected function objToArray($obj) {
        if ($obj instanceof HypixelObject) {
            return $obj->getRaw();
        } else if (is_array($obj)) {
            return $obj;
        }
        throw new InvalidArgumentException();
    }

    /**
     * @param Closure $provider
     * @param $data
     * @return mixed|null
     */
    protected function wrapProvider(Closure $provider, $data) {
        if ($data == null) {
            return null;
        }
        return $provider($this->getHypixelPHP(), $data);
    }
}