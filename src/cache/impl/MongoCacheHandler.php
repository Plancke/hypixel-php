<?php

namespace Plancke\HypixelPHP\cache\impl;

use MongoDB\Client;
use MongoDB\Database;
use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\cache\CacheTypes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\exceptions\InvalidArgumentException;
use Plancke\HypixelPHP\HypixelPHP;
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

/**
 * Implementation for CacheHandler, stores data in MongoDB
 *
 * Class MongoCacheHandler
 * @package HypixelPHP
 */
class MongoCacheHandler extends CacheHandler {

    const FIND_OPTIONS = [
        'typeMap' => [
            'root' => 'array',
            'document' => 'array',
            'array' => 'array'
        ],
        "maxTimeMS" => 1000
    ];
    const UPDATE_OPTIONS = [
        'upsert' => true
    ];
    const SINGLE_SAVE = 'single_save';

    protected $mongoClient;

    public function __construct(HypixelPHP $HypixelPHP, Client $mongoClient) {
        parent::__construct($HypixelPHP);

        $this->mongoClient = $mongoClient;
    }

    /**
     * Run this code once to initialize the indexes on the collections
     *
     * I'm not entirely sure how performant making this call every time is so I'm not taking any chances
     */
    public function ensureIndexes() {
        $db = $this->selectDB();

        $db->selectCollection(CacheTypes::API_KEYS)->createIndex(['record.key' => 1], ['background' => true]);

        $db->selectCollection(CacheTypes::PLAYERS)->createIndex(['record.uuid' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::PLAYERS)->createIndex(['record.playername' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::PLAYERS)->createIndex(['record.knownAliasesLower' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::PLAYER_UUID)->createIndex(['name_lowercase' => 1], ['background' => true]);

        $db->selectCollection(CacheTypes::GUILDS)->createIndex(['record._id' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::GUILDS)->createIndex(['record.name_lower' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::GUILDS_UUID)->createIndex(['uuid' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::GUILDS_NAME)->createIndex(['name_lower' => 1], ['background' => true]);

        $db->selectCollection(CacheTypes::FRIENDS)->createIndex(['record.uuid' => 1], ['background' => true]);

        $db->selectCollection(CacheTypes::SESSIONS)->createIndex(['record.uuid' => 1], ['background' => true]);

        $db->selectCollection(MongoCacheHandler::SINGLE_SAVE)->createIndex(['key' => 1], ['background' => true]);

        return $this;
    }

    /**
     * Select the mongo database to use
     *
     * @param string $db
     * @return Database
     */
    public function selectDB($db = "HypixelPHP") {
        return $this->mongoClient->selectDatabase($db);
    }

    /**
     * @param $key
     * @param $constructor
     * @return HypixelObject|null
     */
    function getSingleSave($key, $constructor) {
        $query = ['key' => $key];
        return $this->wrapProvider($constructor, $this->selectDB()->selectCollection(MongoCacheHandler::SINGLE_SAVE)->findOne($query, self::FIND_OPTIONS));
    }

    /**
     * @param $key
     * @param HypixelObject $hypixelObject
     * @throws InvalidArgumentException
     */
    function setSingleSave($key, HypixelObject $hypixelObject) {
        $query = ['key' => $key];

        $raw = $hypixelObject->getRaw();
        $raw['key'] = $key;

        $this->selectDB()->selectCollection(MongoCacheHandler::SINGLE_SAVE)->replaceOne($query, $this->objToArray($raw), self::UPDATE_OPTIONS);
    }

    /**
     * @param $uuid
     * @return Player|null
     */
    public function getPlayer($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getPlayer(),
            $this->selectDB()->selectCollection(CacheTypes::PLAYERS)->findOne(['record.uuid' => (string)$uuid], MongoCacheHandler::FIND_OPTIONS)
        );
    }

    /**
     * @param Player $player
     * @throws InvalidArgumentException
     */
    public function setPlayer(Player $player) {
        $this->selectDB()->selectCollection(CacheTypes::PLAYERS)->replaceOne(['record.uuid' => (string)$player->getUUID()], $this->objToArray($player), self::UPDATE_OPTIONS);
    }

    /**
     * @param $username
     * @return string|null
     */
    function getUUID($username) {
        $username = strtolower($username);

        // check if player_uuid collection has record
        $query = ['name_lowercase' => $username];
        $data = $this->selectDB()->selectCollection(CacheTypes::PLAYER_UUID)->findOne($query, self::FIND_OPTIONS);
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

        // check if player database has a player for it
        $query = ['record.playername' => $username];
        $data = $this->selectDB()->selectCollection(CacheTypes::PLAYERS)->findOne($query, self::FIND_OPTIONS);
        if ($data != null) {
            $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;
            $diff = time() - $this->getCacheTime(CacheTimes::UUID) - $timestamp;

            $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, "Found name match in PLAYERS! '$diff'");

            if ($diff < 0) {
                if (isset($data['record']['uuid']) && $data['record']['uuid'] != '') {
                    return $data['record']['uuid'];
                }
            }
        } else {
            // check if player database has a old player match for it,
            // only done if there is no direct match, regardless of timestamp on direct match
            $query = ['record.knownAliasesLower' => $username];
            $data = $this->selectDB()->selectCollection(CacheTypes::PLAYERS)->findOne($query, self::FIND_OPTIONS);
            if ($data != null) {
                $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;
                $diff = time() - $this->getCacheTime(CacheTimes::UUID) - $timestamp;

                $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, "Found name match in PLAYERS! '$diff'");

                if ($diff < 0) {
                    if (isset($data['record']['uuid']) && $data['record']['uuid'] != '') {
                        return $data['record']['uuid'];
                    }
                }
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
        $query = ['name_lowercase' => strtolower($username)];
        if ($obj['uuid'] == '' || $obj['uuid'] == null) {
            // still not found, just update time so we don't keep fetching
            //  $this->selectDB()->selectCollection(CollectionNames::PLAYER_UUID, $query, ['$set' => [['timestamp' => time()]]]);
            $this->selectDB()->selectCollection(CacheTypes::PLAYER_UUID)->updateOne($query, ['$set' => ['timestamp' => time()]]);
        } else {
            $this->selectDB()->selectCollection(CacheTypes::PLAYER_UUID)->replaceOne($query, $this->objToArray($obj), self::UPDATE_OPTIONS);
        }
    }

    /**
     * @param $id
     * @return Guild|null
     */
    public function getGuild($id) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getGuild(),
            $this->selectDB()->selectCollection(CacheTypes::GUILDS)->findOne(['record._id' => (string)$id], self::FIND_OPTIONS)
        );
    }

    /**
     * @param Guild $guild
     * @throws InvalidArgumentException
     */
    public function setGuild(Guild $guild) {
        $this->selectDB()->selectCollection(CacheTypes::GUILDS)->replaceOne(['record._id' => (string)$guild->getID()], $this->objToArray($guild), self::UPDATE_OPTIONS);
    }

    /**
     * @param $uuid
     * @return string|null
     */
    function getGuildIDForUUID($uuid) {
        // TODO Do we really need this collection?
        //  Could just check members object inside the documents?
        $data = $this->selectDB()->selectCollection(CacheTypes::GUILDS_UUID)->findOne(['uuid' => (string)$uuid], self::FIND_OPTIONS);
        if ($data != null) {
            if (isset($data['uuid']) && $data['uuid'] != null && $data['uuid'] != '') {
                $cacheTime = $this->getCacheTime(CacheTimes::GUILD);
            } else {
                $cacheTime = $this->getCacheTime(CacheTimes::GUILD_NOT_FOUND);
            }
            $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;
            if (CacheUtil::isExpired($timestamp, $cacheTime)) return null;
            return $data['guild'];
        }
        return null;
    }

    /**
     * @param $uuid
     * @param $obj
     * @throws InvalidArgumentException
     */
    function setGuildIDForUUID($uuid, $obj) {
        $query = ['uuid' => (string)$uuid];
        $this->selectDB()->selectCollection(CacheTypes::GUILDS_UUID)->replaceOne($query, $this->objToArray($obj), self::UPDATE_OPTIONS);
    }

    /**
     * @param $name
     * @return string|null
     */
    function getGuildIDForName($name) {
        $query = ['record.name_lower' => strtolower((string)$name)];
        $data = $this->selectDB()->selectCollection(CacheTypes::GUILDS)->findOne($query, self::FIND_OPTIONS);
        if ($data != null) {
            $cacheTime = $this->getCacheTime(CacheTimes::GUILD);
            $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;

            if (!CacheUtil::isExpired($timestamp, $cacheTime)) {
                // it's not expired, return guild directly
                return $this->wrapProvider($this->getHypixelPHP()->getProvider()->getGuild(), $data);
            }
        }

        $query = ['name_lower' => strtolower((string)$name)];
        $data = $this->selectDB()->selectCollection(CacheTypes::GUILDS_NAME)->findOne($query, self::FIND_OPTIONS);
        if ($data != null) {
            if (isset($data['name_lower']) && $data['name_lower'] != null && $data['name_lower'] != '') {
                $cacheTime = $this->getCacheTime(CacheTimes::GUILD);
            } else {
                $cacheTime = $this->getCacheTime(CacheTimes::GUILD_NOT_FOUND);
            }
            $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;
            if (CacheUtil::isExpired($timestamp, $cacheTime)) return null;
            return $data['guild'];
        }
        return null;
    }

    /**
     * @param $name
     * @param $obj
     * @throws InvalidArgumentException
     */
    function setGuildIDForName($name, $obj) {
        $query = ['name_lower' => strtolower((string)$name)];
        $this->selectDB()->selectCollection(CacheTypes::GUILDS_NAME)->replaceOne($query, $this->objToArray($obj), self::UPDATE_OPTIONS);
    }

    /**
     * @param $uuid
     * @return Friends|null
     */
    public function getFriends($uuid) {
        $query = ['record.uuid' => (string)$uuid];
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getFriends(),
            $this->selectDB()->selectCollection(CacheTypes::FRIENDS)->findOne($query, self::FIND_OPTIONS)
        );
    }

    /**
     * @param Friends $friends
     * @throws InvalidArgumentException
     */
    public function setFriends(Friends $friends) {
        $query = ['record.uuid' => (string)$friends->getUUID()];
        $this->selectDB()->selectCollection(CacheTypes::FRIENDS)->replaceOne($query, $this->objToArray($friends), self::UPDATE_OPTIONS);
    }

    /**
     * @param $uuid
     * @return Session|null
     */
    public function getSession($uuid) {
        $query = ['record.uuid' => (string)$uuid];
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getSession(),
            $this->selectDB()->selectCollection(CacheTypes::SESSIONS)->findOne($query, self::FIND_OPTIONS)
        );
    }

    /**
     * @param Session $session
     * @throws InvalidArgumentException
     */
    public function setSession(Session $session) {
        $query = ['record.uuid' => (string)$session->getUUID()];
        $this->selectDB()->selectCollection(CacheTypes::SESSIONS)->replaceOne($query, $this->objToArray($session), self::UPDATE_OPTIONS);
    }

    /**
     * @param $key
     * @return KeyInfo|null
     */
    public function getKeyInfo($key) {
        $query = ['record.key' => (string)$key];
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getKeyInfo(),
            $this->selectDB()->selectCollection(CacheTypes::API_KEYS)->findOne($query, self::FIND_OPTIONS)
        );
    }

    /**
     * @param KeyInfo $keyInfo
     * @throws InvalidArgumentException
     */
    public function setKeyInfo(KeyInfo $keyInfo) {
        $query = ['record.key' => (string)$keyInfo->getKey()];
        $this->selectDB()->selectCollection(CacheTypes::API_KEYS)->replaceOne($query, $this->objToArray($keyInfo), self::UPDATE_OPTIONS);
    }


    /**
     * @return Leaderboards|null
     */
    public function getLeaderboards() {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getSingleSave(CacheTypes::LEADERBOARDS, $this->getHypixelPHP()->getProvider()->getLeaderboards());
    }

    /**
     * @param Leaderboards $leaderboards
     * @throws InvalidArgumentException
     */
    public function setLeaderboards(Leaderboards $leaderboards) {
        $this->setSingleSave(CacheTypes::LEADERBOARDS, $leaderboards);
    }


    /**
     * @return Boosters|null
     */
    public function getBoosters() {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getSingleSave(CacheTypes::BOOSTERS, $this->getHypixelPHP()->getProvider()->getBoosters());
    }

    /**
     * @param Boosters $boosters
     * @throws InvalidArgumentException
     */
    public function setBoosters(Boosters $boosters) {
        $this->setSingleSave(CacheTypes::BOOSTERS, $boosters);
    }


    /**
     * @return WatchdogStats|null
     */
    public function getWatchdogStats() {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getSingleSave(CacheTypes::WATCHDOG_STATS, $this->getHypixelPHP()->getProvider()->getWatchdogStats());
    }

    /**
     * @param WatchdogStats $watchdogStats
     * @throws InvalidArgumentException
     */
    public function setWatchdogStats(WatchdogStats $watchdogStats) {
        $this->setSingleSave(CacheTypes::WATCHDOG_STATS, $watchdogStats);
    }

    /**
     * @return PlayerCount|null
     */
    public function getPlayerCount() {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getSingleSave(CacheTypes::PLAYER_COUNT, $this->getHypixelPHP()->getProvider()->getPlayerCount());
    }

    /**
     * @param PlayerCount $playerCount
     * @throws InvalidArgumentException
     */
    public function setPlayerCount(PlayerCount $playerCount) {
        $this->setSingleSave(CacheTypes::PLAYER_COUNT, $playerCount);
    }

    /**
     * @return GameCounts|null
     */
    public function getGameCounts() {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getSingleSave(CacheTypes::GAME_COUNTS, $this->getHypixelPHP()->getProvider()->getGameCounts());
    }

    /**
     * @param GameCounts $gameCounts
     * @throws InvalidArgumentException
     */
    public function setGameCounts(GameCounts $gameCounts) {
        $this->setSingleSave(CacheTypes::GAME_COUNTS, $gameCounts);
    }
}