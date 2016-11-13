<?php

namespace Plancke\HypixelPHP\cache\impl\mongo;

use MongoDB\Client;
use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\responses\WatchdogStats;

/**
 * Implementation for CacheHandler, stores data in MongoDB
 *
 * Class MongoCacheHandler
 * @package HypixelPHP
 */
class AMongoCacheHandler extends CacheHandler {

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

        $db->selectCollection(CollectionNames::API_KEYS)->createIndex(['record.key'], ['background' => true]);

        $db->selectCollection(CollectionNames::PLAYERS)->createIndex(['record.uuid'], ['background' => true]);
        $db->selectCollection(CollectionNames::PLAYERS)->createIndex(['record.playername'], ['background' => true]);
        $db->selectCollection(CollectionNames::PLAYER_UUID)->createIndex(['name_lowercase'], ['background' => true]);

        $db->selectCollection(CollectionNames::GUILDS)->createIndex(['record._id'], ['background' => true]);
        $db->selectCollection(CollectionNames::GUILDS)->createIndex(['extra.name_lower'], ['background' => true]);
        $db->selectCollection(CollectionNames::GUILDS_UUID)->createIndex(['uuid'], ['background' => true]);
        $db->selectCollection(CollectionNames::GUILDS_NAME)->createIndex(['name_lower'], ['background' => true]);

        $db->selectCollection(CollectionNames::FRIENDS)->createIndex(['record.uuid'], ['background' => true]);

        $db->selectCollection(CollectionNames::SESSIONS)->createIndex(['record.uuid'], ['background' => true]);

        $db->selectCollection(CollectionNames::SINGLE_SAVE)->createIndex(['key'], ['background' => true]);

        return $this;
    }

    /**
     * @return \MongoDB
     */
    public function selectDB() {
        return $this->mongoClient->selectDatabase("HypixelPHP");
    }

    /**
     * @param $collection
     * @param $query
     * @param $obj
     */
    public function updateCollection($collection, $query, $obj) {
        if ($obj instanceof HypixelObject) {
            $this->selectDB()->createCollection($collection)->update($query, $obj->getRaw(), ['upsert' => true]);
        } else {
            $this->selectDB()->createCollection($collection)->update($query, $obj, ['upsert' => true]);
        }
    }

    /**
     * @param $collection
     * @param $query
     * @return array|null
     */
    public function queryCollection($collection, $query) {
        /** @noinspection PhpUndefinedFieldInspection */
        return $this->selectDB()->createCollection($collection)->findOne($query);
    }

    /**
     * @param $key
     * @param HypixelObject $hypixelObject
     */
    function setSingleSave($key, HypixelObject $hypixelObject) {
        $query = ['key' => $key];
        $this->updateCollection(CollectionNames::SINGLE_SAVE, $query, $hypixelObject);
    }

    /**
     * @param $key
     * @param $constructor
     * @return HypixelObject|null
     */
    function getSingleSave($key, $constructor) {
        $query = ['key' => $key];
        $data = $this->queryCollection(CollectionNames::SINGLE_SAVE, $query);
        if ($data != null) {
            return $constructor($data, $this->getHypixelPHP());
        }
        return null;
    }

    public function setCachedPlayer(Player $player) {
        $query = ['record.uuid' => (string)$player->getUUID()];
        $this->updateCollection(CollectionNames::PLAYERS, $query, $player);
    }

    function getCachedPlayer($uuid) {
        $query = ['record.uuid' => (string)$uuid];
        $data = $this->queryCollection(CollectionNames::PLAYERS, $query);
        if ($data != null) {
            $closure = $this->getHypixelPHP()->getProvider()->getPlayer();
            return $closure($this->getHypixelPHP(), $data);
        }
        return null;
    }

    function setPlayerUUID($username, $obj) {
        $query = ['name_lowercase' => strtolower($username)];
        if ($obj['uuid'] == '' || $obj['uuid'] == null) {
            // still not found, just update time so we don't keep fetching
            $this->updateCollection(CollectionNames::PLAYER_UUID, $query, ['$set' => [['timestamp' => time()]]]);
        } else {
            $this->updateCollection(CollectionNames::PLAYER_UUID, $query, $obj);
        }
    }

    function getUUID($username) {
        $username = strtolower($username);

        // check if player_uuid collection has record
        $query = ['name_lowercase' => $username];
        $data = $this->queryCollection(CollectionNames::PLAYER_UUID, $query);
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

        // check if player database has a player for it
        $query = ['record.playername' => $username];
        $data = $this->queryCollection(CollectionNames::PLAYERS, $query);
        if ($data != null) {
            $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;
            $diff = time() - $this->getCacheTime(CacheTimes::UUID) - $timestamp;

            $this->getHypixelPHP()->getLogger()->log("Found name match in PLAYERS! '$diff'");

            if ($diff < 0) {
                if (isset($data['record']['uuid']) && $data['record']['uuid'] != '') {
                    return $data['record']['uuid'];
                }
            }
        }
        return null;
    }

    function setCachedGuild(Guild $guild) {
        $query = ['record._id' => (string)$guild->getID()];
        $this->updateCollection(CollectionNames::GUILDS, $query, $guild);
    }

    function getCachedGuild($id) {
        $query = ['record._id' => (string)$id];
        $data = $this->queryCollection(CollectionNames::GUILDS, $query);
        if ($data != null) {
            $closure = $this->getHypixelPHP()->getProvider()->getGuild();
            return $closure($this->getHypixelPHP(), $data);
        }
        return null;
    }

    function setGuildIDForUUID($uuid, $obj) {
        $query = ['uuid' => (string)$uuid];
        $this->updateCollection(CollectionNames::GUILDS_UUID, $query, $obj);
    }

    function getGuildIDForUUID($uuid) {
        $query = ['uuid' => (string)$uuid];
        $data = $this->queryCollection(CollectionNames::GUILDS_UUID, $query);
        if ($data != null) {
            if (isset($data['uuid']) && $data['uuid'] != null && $data['uuid'] != '') {
                $cacheTime = $this->getCacheTime(CacheTimes::GUILD);
            } else {
                $cacheTime = $this->getCacheTime(CacheTimes::GUILD_NOT_FOUND);
            }
            $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;
            $diff = time() - $cacheTime - $timestamp;

            if ($diff < 0) {
                return $data['guild'];
            }
        }
        return null;
    }

    function setGuildIDForName($name, $obj) {
        $query = ['name_lower' => strtolower((string)$name)];
        $this->updateCollection(CollectionNames::GUILDS_NAME, $query, $obj);
    }

    function getGuildIDForName($name) {
        $query = ['extra.name_lower' => strtolower((string)$name)];
        $data = $this->queryCollection(CollectionNames::GUILDS, $query);
        if ($data != null) {
            $cacheTime = $this->getCacheTime(CacheTimes::GUILD);
            $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;
            $diff = time() - $cacheTime - $timestamp;

            if ($diff < 0) {
                $closure = $this->getHypixelPHP()->getProvider()->getGuild();
                return $closure($this->getHypixelPHP(), $data);
            }
        }

        $query = ['name_lower' => strtolower((string)$name)];
        $data = $this->queryCollection(CollectionNames::GUILDS_NAME, $query);
        if ($data != null) {
            if (isset($data['name_lower']) && $data['name_lower'] != null && $data['name_lower'] != '') {
                $cacheTime = $this->getCacheTime(CacheTimes::GUILD);
            } else {
                $cacheTime = $this->getCacheTime(CacheTimes::GUILD_NOT_FOUND);
            }
            $timestamp = array_key_exists('timestamp', $data) ? $data['timestamp'] : 0;
            $diff = time() - $cacheTime - $timestamp;

            if ($diff < 0) {
                return $data['guild'];
            }
        }
        return null;
    }

    function setCachedFriends(Friends $friends) {
        $query = ['record.uuid' => (string)$friends->getUUID()];
        $this->updateCollection(CollectionNames::FRIENDS, $query, $friends);
    }

    function getCachedFriends($uuid) {
        $query = ['record.uuid' => (string)$uuid];
        $data = $this->queryCollection(CollectionNames::FRIENDS, $query);
        if ($data != null) {
            $closure = $this->getHypixelPHP()->getProvider()->getFriends();
            return $closure($this->getHypixelPHP(), $data);
        }
        return null;
    }

    function setCachedSession(Session $session) {
        $query = ['record.uuid' => (string)$session->getUUID()];
        $this->updateCollection(CollectionNames::SESSIONS, $query, $session);
    }

    function getCachedSession($uuid) {
        $query = ['record.uuid' => (string)$uuid];
        $data = $this->queryCollection(CollectionNames::SESSIONS, $query);
        if ($data != null) {
            $closure = $this->getHypixelPHP()->getProvider()->getSession();
            return $closure($this->getHypixelPHP(), $data);
        }
        return null;
    }

    function setCachedKeyInfo(KeyInfo $keyInfo) {
        $query = ['record.key' => (string)$keyInfo->getKey()];
        $this->updateCollection(CollectionNames::API_KEYS, $query, $keyInfo);
    }

    function getCachedKeyInfo($key) {
        $query = ['record.key' => (string)$key];
        $data = $this->queryCollection(CollectionNames::API_KEYS, $query);
        if ($data != null) {
            $closure = $this->getHypixelPHP()->getProvider()->getKeyInfo();
            return $closure($this->getHypixelPHP(), $data);
        }
        return null;
    }

    function setCachedLeaderboards(Leaderboards $leaderboards) {
        $this->setSingleSave(SingleSaveKeys::LEADERBOARDS, $leaderboards);
    }

    function getCachedLeaderboards() {
        return $this->getSingleSave(SingleSaveKeys::LEADERBOARDS, $this->getHypixelPHP()->getProvider()->getLeaderboards());
    }

    function setCachedBoosters(Boosters $boosters) {
        $this->setSingleSave(SingleSaveKeys::BOOSTERS, $boosters);
    }

    function getCachedBoosters() {
        return $this->getSingleSave(SingleSaveKeys::BOOSTERS, $this->getHypixelPHP()->getProvider()->getBoosters());
    }

    function setCachedWatchdogStats(WatchdogStats $watchdogStats) {
        $this->setSingleSave(SingleSaveKeys::WATCHDOG_STATS, $watchdogStats);
    }

    function getCachedWatchdogStats() {
        return $this->getSingleSave(SingleSaveKeys::WATCHDOG_STATS, $this->getHypixelPHP()->getProvider()->getWatchdogStats());
    }
}