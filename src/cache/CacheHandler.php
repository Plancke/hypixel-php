<?php

namespace Plancke\HypixelPHP\cache;

use Closure;
use InvalidArgumentException;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\classes\Module;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\counts\Counts;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\PunishmentStats;
use Plancke\HypixelPHP\responses\RecentGames;
use Plancke\HypixelPHP\responses\Resource;
use Plancke\HypixelPHP\responses\skyblock\SkyBlockProfile;
use Plancke\HypixelPHP\responses\Status;

/**
 * Class CacheHandler
 * @package Plancke\HypixelPHP\cache
 */
abstract class CacheHandler extends Module {

    // cache time to only get cache or null if not present
    const MAX_CACHE_TIME = PHP_INT_MAX;

    protected $cacheTimes = [
        CacheTimes::RESOURCES => 3 * 60 * 60,

        CacheTimes::PLAYER => 10 * 60,
        CacheTimes::UUID => 6 * 60 * 60,
        CacheTimes::UUID_NOT_FOUND => 2 * 60 * 60,

        CacheTimes::GUILD => 10 * 60,
        CacheTimes::GUILD_NOT_FOUND => 10 * 60,

        CacheTimes::LEADERBOARDS => 10 * 60,
        CacheTimes::BOOSTERS => 10 * 60,
        CacheTimes::STATUS => 10 * 60,
        CacheTimes::KEY_INFO => 10 * 60,
        CacheTimes::FRIENDS => 10 * 60,
        CacheTimes::PUNISHMENT_STATS => 10 * 60,
        CacheTimes::COUNTS => 10 * 60,

        CacheTimes::SKYBLOCK_PROFILE => 10 * 60
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
     * @param $resource
     * @return Resource
     */
    public abstract function getResource($resource);

    /**
     * @param Resource $resource
     * @return void
     */
    public abstract function setResource($resource);

    /**
     * @param $uuid
     * @return Player|null
     */
    public abstract function getPlayer($uuid);

    /**
     * @param Player $player
     * @return void
     */
    public abstract function setPlayer(Player $player);

    /**
     * @param $username
     * @return string|null
     */
    public abstract function getUUID($username);

    /**
     * @param $username
     * @param $uuid
     * @return void
     */
    public abstract function setPlayerUUID($username, $uuid);

    /**
     * @param $id
     * @return Guild|null
     */
    public abstract function getGuild($id);

    /**
     * @param Guild $guild
     * @return void
     */
    public abstract function setGuild(Guild $guild);

    /**
     * @param $uuid
     * @return Guild|string|null
     */
    public abstract function getGuildIDForUUID($uuid);

    /**
     * @param $uuid
     * @param $id
     * @return void
     */
    public abstract function setGuildIDForUUID($uuid, $id);

    /**
     * @param $name
     * @return Guild|string|null
     */
    public abstract function getGuildIDForName($name);

    /**
     * @param $name
     * @param $id
     * @return void
     */
    public abstract function setGuildIDForName($name, $id);

    /**
     * @param $uuid
     * @return Friends|null
     */
    public abstract function getFriends($uuid);

    /**
     * @param Friends $friends
     * @return void
     */
    public abstract function setFriends(Friends $friends);

    /**
     * @param $uuid
     * @return Status|null
     */
    public abstract function getStatus($uuid);

    /**
     * @param Status $status
     * @return void
     */
    public abstract function setStatus(Status $status);

    /**
     * @param $uuid
     * @return RecentGames|null
     */
    public abstract function getRecentGames($uuid);

    /**
     * @param RecentGames $recentGames
     * @return void
     */
    public abstract function setRecentGames(RecentGames $recentGames);

    /**
     * @param $key
     * @return KeyInfo|null
     */
    public abstract function getKeyInfo($key);

    /**
     * @param KeyInfo $keyInfo
     * @return void
     */
    public abstract function setKeyInfo(KeyInfo $keyInfo);

    /**
     * @return Leaderboards|null
     */
    public abstract function getLeaderboards();

    /**
     * @param Leaderboards $leaderboards
     * @return void
     */
    public abstract function setLeaderboards(Leaderboards $leaderboards);

    /**
     * @return Boosters|null
     */
    public abstract function getBoosters();

    /**
     * @param Boosters $boosters
     * @return void
     */
    public abstract function setBoosters(Boosters $boosters);

    /**
     * @return PunishmentStats|null
     */
    public abstract function getPunishmentStats();

    /**
     * @param PunishmentStats $punishmentStats
     * @return void
     */
    public abstract function setPunishmentStats(PunishmentStats $punishmentStats);

    /**
     * @return Counts|null
     */
    public abstract function getCounts();

    /**
     * @param Counts $gameCounts
     * @return void
     */
    public abstract function setCounts(Counts $gameCounts);

    /**
     * @param $profile_id
     * @return SkyBlockProfile|null
     */
    public abstract function getSkyBlockProfile($profile_id);

    /**
     * @param SkyBlockProfile $profile
     * @return void
     */
    public abstract function setSkyBlockProfile(SkyBlockProfile $profile);

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
        if ($data == null) return null;
        return $provider($this->getHypixelPHP(), $data);
    }
}