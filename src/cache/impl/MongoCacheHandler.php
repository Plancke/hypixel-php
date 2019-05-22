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
     * @param $collection
     * @param $query
     * @return array|null
     */
    public function queryCollection($collection, $query) {
        return $this->selectDB()->selectCollection($collection)->findOne($query, [
            'typeMap' => [
                'root' => 'array',
                'document' => 'array',
                'array' => 'array'
            ],
            "maxTimeMS" => 1000
        ]);
    }

    /**
     * Replace a single document for given query,
     * creates the document if it doesn't exist yet.
     *
     * @param $collection
     * @param $query
     * @param $obj
     * @throws InvalidArgumentException
     */
    public function updateCollection($collection, $query, $obj) {
        $this->selectDB()->selectCollection($collection)->replaceOne($query, $this->objToArray($obj), ['upsert' => true]);
    }

    /**
     * @param $key
     * @param $constructor
     * @return HypixelObject|null
     */
    function getSingleSave($key, $constructor) {
        $query = ['key' => $key];
        return $this->wrapProvider($constructor, $this->queryCollection(MongoCacheHandler::SINGLE_SAVE, $query));
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

        $this->updateCollection(MongoCacheHandler::SINGLE_SAVE, $query, $raw);
    }

    /**
     * @param $uuid
     * @return Player|null
     */
    public function getPlayer($uuid) {
        $query = ['record.uuid' => (string)$uuid];
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getPlayer(),
            $this->queryCollection(CacheTypes::PLAYERS, $query)
        );
    }

    /**
     * @param Player $player
     * @throws InvalidArgumentException
     */
    public function setPlayer(Player $player) {
        $query = ['record.uuid' => (string)$player->getUUID()];
        $this->updateCollection(CacheTypes::PLAYERS, $query, $player);
    }

    /**
     * @param $username
     * @return string|null
     */
    function getUUID($username) {
        $username = strtolower($username);

        // check if player_uuid collection has record
        $query = ['name_lowercase' => $username];
        $data = $this->queryCollection(CacheTypes::PLAYER_UUID, $query);
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
        $data = $this->queryCollection(CacheTypes::PLAYERS, $query);
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
            $data = $this->queryCollection(CacheTypes::PLAYERS, $query);
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
            // $this->updateCollection(CollectionNames::PLAYER_UUID, $query, ['$set' => [['timestamp' => time()]]]);
            $this->selectDB()->selectCollection(CacheTypes::PLAYER_UUID)->updateOne($query, ['$set' => ['timestamp' => time()]]);
        } else {
            $this->updateCollection(CacheTypes::PLAYER_UUID, $query, $obj);
        }
    }

    /**
     * @param $id
     * @return Guild|null
     */
    public function getGuild($id) {
        $query = ['record._id' => (string)$id];
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getGuild(),
            $this->queryCollection(CacheTypes::GUILDS, $query)
        );
    }

    /**
     * @param Guild $guild
     * @throws InvalidArgumentException
     */
    public function setGuild(Guild $guild) {
        $query = ['record._id' => (string)$guild->getID()];
        $this->updateCollection(CacheTypes::GUILDS, $query, $guild);
    }

    /**
     * @param $uuid
     * @return string|null
     */
    function getGuildIDForUUID($uuid) {
        $query = ['uuid' => (string)$uuid];
        // TODO Do we really need this collection?
        //  Could just check members object inside the documents?
        $data = $this->queryCollection(CacheTypes::GUILDS_UUID, $query);
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
        $this->updateCollection(CacheTypes::GUILDS_UUID, $query, $obj);
    }

    /**
     * @param $name
     * @return string|null
     */
    function getGuildIDForName($name) {
        $query = ['record.name_lower' => strtolower((string)$name)];
        $data = $this->queryCollection(CacheTypes::GUILDS, $query);
        if ($data != null) {
            $cacheTime = $this->getCacheTime(CacheTimes::GUILD);
            $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;

            if (!CacheUtil::isExpired($timestamp, $cacheTime)) {
                // it's not expired, return guild directly
                return $this->wrapProvider($this->getHypixelPHP()->getProvider()->getGuild(), $data);
            }
        }

        $query = ['name_lower' => strtolower((string)$name)];
        $data = $this->queryCollection(CacheTypes::GUILDS_NAME, $query);
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
        $this->updateCollection(CacheTypes::GUILDS_NAME, $query, $obj);
    }

    /**
     * @param $uuid
     * @return Friends|null
     */
    public function getFriends($uuid) {
        $query = ['record.uuid' => (string)$uuid];
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getFriends(),
            $this->queryCollection(CacheTypes::FRIENDS, $query)
        );
    }

    /**
     * @param Friends $friends
     * @throws InvalidArgumentException
     */
    public function setFriends(Friends $friends) {
        $query = ['record.uuid' => (string)$friends->getUUID()];
        $this->updateCollection(CacheTypes::FRIENDS, $query, $friends);
    }

    /**
     * @param $uuid
     * @return Session|null
     */
    public function getSession($uuid) {
        $query = ['record.uuid' => (string)$uuid];
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getSession(),
            $this->queryCollection(CacheTypes::SESSIONS, $query)
        );
    }

    /**
     * @param Session $session
     * @throws InvalidArgumentException
     */
    public function setSession(Session $session) {
        $query = ['record.uuid' => (string)$session->getUUID()];
        $this->updateCollection(CacheTypes::SESSIONS, $query, $session);
    }

    /**
     * @param $key
     * @return KeyInfo|null
     */
    public function getKeyInfo($key) {
        $query = ['record.key' => (string)$key];
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getKeyInfo(),
            $this->queryCollection(CacheTypes::API_KEYS, $query)
        );
    }

    /**
     * @param KeyInfo $keyInfo
     * @throws InvalidArgumentException
     */
    public function setKeyInfo(KeyInfo $keyInfo) {
        $query = ['record.key' => (string)$keyInfo->getKey()];
        $this->updateCollection(CacheTypes::API_KEYS, $query, $keyInfo);
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