<?php

namespace Plancke\HypixelPHP\cache\impl;

use InvalidArgumentException;
use MongoDB\Client;
use MongoDB\Database;
use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\cache\CacheTypes;
use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\RecentGames;
use Plancke\HypixelPHP\responses\skyblock\SkyBlockProfile;
use Plancke\HypixelPHP\responses\Status;

/**
 * Implementation for CacheHandler, stores data in MongoDB
 *
 * Class MongoCacheHandler
 * @package HypixelPHP
 */
class MongoCacheHandler extends FlatFileCacheHandler {

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
    public function ensureIndexes(): MongoCacheHandler {
        $db = $this->selectDB();

        $db->selectCollection(CacheTypes::API_KEYS)->createIndex(['record.key' => 1], ['background' => true]);

        $db->selectCollection(CacheTypes::PLAYERS)->createIndex(['record.uuid' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::PLAYERS)->createIndex(['record.playername' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::PLAYERS)->createIndex(['record.knownAliasesLower' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::PLAYER_UUID)->createIndex(['name_lowercase' => 1], ['background' => true]);

        $db->selectCollection(CacheTypes::GUILDS)->createIndex(['record._id' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::GUILDS)->createIndex(['record.name_lower' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::GUILDS)->createIndex(['record.members.uuid' => 1], ['background' => true]);

        $db->selectCollection(CacheTypes::STATUS)->createIndex(['record.uuid' => 1], ['background' => true]);
        $db->selectCollection(CacheTypes::RECENT_GAMES)->createIndex(['record.uuid' => 1], ['background' => true]);

        $db->selectCollection(CacheTypes::SKYBLOCK_PROFILES)->createIndex(['record.profile_id' => 1], ['background' => true]);

        return $this;
    }

    /**
     * Cleanup old data
     */
    public function cleanup(): MongoCacheHandler {
        $db = $this->selectDB();

        $db->selectCollection('guilds_uuid')->drop();
        $db->selectCollection('guilds_name')->drop();

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
     * @param $uuid
     * @return Player|null
     */
    public function getPlayer($uuid) {
        if (empty($uuid)) {
            throw new InvalidArgumentException("Player UUID cannot be empty");
        }
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getPlayer(),
            $this->selectDB()->selectCollection(CacheTypes::PLAYERS)->findOne(
                ['record.uuid' => (string)$uuid], self::FIND_OPTIONS
            )
        );
    }

    /**
     * @param Player $player
     * @throws InvalidArgumentException
     */
    public function setPlayer(Player $player) {
        $this->selectDB()->selectCollection(CacheTypes::PLAYERS)->replaceOne(
            ['record.uuid' => (string)$player->getUUID()], $this->objToArray($player), self::UPDATE_OPTIONS
        );
    }

    /**
     * @param $username
     * @return string|null
     */
    function getUUID($username) {
        $username = strtolower($username);

        // check if player_uuid collection has record
        $data = $this->selectDB()->selectCollection(CacheTypes::PLAYER_UUID)->findOne(
            ['name_lowercase' => $username], self::FIND_OPTIONS
        );
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
        $data = $this->selectDB()->selectCollection(CacheTypes::PLAYERS)->findOne(
            ['record.playername' => $username], self::FIND_OPTIONS
        );
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
            $data = $this->selectDB()->selectCollection(CacheTypes::PLAYERS)->findOne(
                ['record.knownAliasesLower' => $username], self::FIND_OPTIONS
            );
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
        if (empty($id)) {
            throw new InvalidArgumentException("Guild ID cannot be empty");
        }
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getGuild(),
            $this->selectDB()->selectCollection(CacheTypes::GUILDS)->findOne(
                ['record._id' => (string)$id], self::FIND_OPTIONS
            )
        );
    }

    /**
     * @param $uuid
     * @return Guild|null
     */
    public function getGuildByPlayer($uuid) {
        if (empty($uuid)) {
            throw new InvalidArgumentException("Guild member cannot be empty");
        }
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getGuild(),
            $this->selectDB()->selectCollection(CacheTypes::GUILDS)->findOne(
                ['record.members.uuid' => (string)$uuid], self::FIND_OPTIONS
            )
        );
    }

    /**
     * @param $name
     * @return Guild|null
     */
    public function getGuildByName($name) {
        if (empty($name)) {
            throw new InvalidArgumentException("Guild name cannot be empty");
        }
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getGuild(),
            $this->selectDB()->selectCollection(CacheTypes::GUILDS)->findOne(
                ['record.name_lower' => strtolower((string)$name)], self::FIND_OPTIONS
            )
        );
    }

    /**
     * @param Guild $guild
     * @throws InvalidArgumentException
     */
    public function setGuild(Guild $guild) {
        if (empty($guild->getID())) {
            throw new InvalidArgumentException("Guild ID cannot be empty");
        }
        $this->selectDB()->selectCollection(CacheTypes::GUILDS)->replaceOne(
            ['record._id' => $guild->getID()], $this->objToArray($guild), self::UPDATE_OPTIONS
        );
    }

    /**
     * @param $uuid
     * @return Status|null
     */
    public function getStatus($uuid) {
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getStatus(),
            $this->selectDB()->selectCollection(CacheTypes::STATUS)->findOne(
                ['record.uuid' => (string)$uuid], self::FIND_OPTIONS
            )
        );
    }

    /**
     * @param Status $status
     * @throws InvalidArgumentException
     */
    public function setStatus(Status $status) {
        $this->selectDB()->selectCollection(CacheTypes::STATUS)->replaceOne(
            ['record.uuid' => (string)$status->getUUID()], $this->objToArray($status), self::UPDATE_OPTIONS
        );
    }

    /**
     * @param $uuid
     * @return RecentGames|null
     */
    public function getRecentGames($uuid) {
        if (empty($uuid)) {
            throw new InvalidArgumentException("Recent games UUID cannot be empty");
        }
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getRecentGames(),
            $this->selectDB()->selectCollection(CacheTypes::RECENT_GAMES)->findOne(
                ['record.uuid' => (string)$uuid], self::FIND_OPTIONS
            )
        );
    }

    /**
     * @param RecentGames $recentGames
     * @throws InvalidArgumentException
     */
    public function setRecentGames(RecentGames $recentGames) {
        $this->selectDB()->selectCollection(CacheTypes::RECENT_GAMES)->replaceOne(
            ['record.uuid' => (string)$recentGames->getUUID()], $this->objToArray($recentGames), self::UPDATE_OPTIONS
        );
    }

    /**
     * @param $key
     * @return KeyInfo|null
     */
    public function getKeyInfo($key) {
        if (empty($key)) {
            throw new InvalidArgumentException("API key cannot be empty");
        }
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getKeyInfo(),
            $this->selectDB()->selectCollection(CacheTypes::API_KEYS)->findOne(
                ['record.key' => (string)$key], self::FIND_OPTIONS
            )
        );
    }

    /**
     * @param KeyInfo $keyInfo
     * @throws InvalidArgumentException
     */
    public function setKeyInfo(KeyInfo $keyInfo) {
        $this->selectDB()->selectCollection(CacheTypes::API_KEYS)->replaceOne(
            ['record.key' => (string)$keyInfo->getKey()], $this->objToArray($keyInfo), self::UPDATE_OPTIONS
        );
    }

    /**
     * @param $profile_id
     * @return SkyBlockProfile|null
     */
    public function getSkyBlockProfile($profile_id) {
        if (empty($profile_id)) {
            throw new InvalidArgumentException("SkyBlock profile ID cannot be empty");
        }
        return $this->wrapProvider(
            $this->getHypixelPHP()->getProvider()->getSkyBlockProfile(),
            $this->selectDB()->selectCollection(CacheTypes::SKYBLOCK_PROFILES)->findOne(
                ['record.profile_id' => (string)$profile_id], self::FIND_OPTIONS
            )
        );
    }

    /**
     * @param SkyBlockProfile $profile
     * @return void
     * @throws InvalidArgumentException
     */
    public function setSkyBlockProfile(SkyBlockProfile $profile) {
        $this->selectDB()->selectCollection(CacheTypes::SKYBLOCK_PROFILES)->replaceOne(
            ['record.profile_id' => (string)$profile->getProfileId()], $this->objToArray($profile), self::UPDATE_OPTIONS
        );
    }
}