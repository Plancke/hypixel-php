<?php

namespace Plancke\HypixelPHP;

use Exception;
use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\cache\impl\FlatFileCacheHandler;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\exceptions\BadResponseCodeException;
use Plancke\HypixelPHP\exceptions\ExceptionCodes;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\exceptions\InvalidUUIDException;
use Plancke\HypixelPHP\exceptions\NoPairsException;
use Plancke\HypixelPHP\fetch\Fetcher;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\fetch\FetchTypes;
use Plancke\HypixelPHP\fetch\impl\DefaultFetcher;
use Plancke\HypixelPHP\fetch\Response;
use Plancke\HypixelPHP\log\impl\NoLogger;
use Plancke\HypixelPHP\log\Logger;
use Plancke\HypixelPHP\provider\Provider;
use Plancke\HypixelPHP\resources\ResourceManager;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\gameCounts\GameCounts;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\PlayerCount;
use Plancke\HypixelPHP\responses\RecentGames;
use Plancke\HypixelPHP\responses\Resource;
use Plancke\HypixelPHP\responses\skyblock\SkyBlockProfile;
use Plancke\HypixelPHP\responses\Status;
use Plancke\HypixelPHP\responses\PunishmentStats;
use Plancke\HypixelPHP\util\InputType;
use Plancke\HypixelPHP\util\Utilities;
use Plancke\HypixelPHP\util\Validator;

/**
 * HypixelPHP
 *
 * @author Plancke
 * @link https://plancke.io
 *
 */
class HypixelPHP {

    protected $apiKey;
    protected $options;

    protected $logger;
    protected $fetcher;
    protected $cacheHandler;
    protected $provider;
    protected $resourceManager;

    /**
     * @param string $apiKey
     * @throws HypixelPHPException
     */
    public function __construct($apiKey) {
        $this->setAPIKey($apiKey);

        $this->setLogger(new NoLogger($this));
        $this->setFetcher(new DefaultFetcher($this));
        $this->setCacheHandler(new FlatFileCacheHandler($this));
        $this->setProvider(new Provider($this));
        $this->setResourceManager(new ResourceManager($this));
    }

    /**
     * @return string
     */
    public function getAPIKey() {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return $this
     * @throws HypixelPHPException
     */
    public function setAPIKey($apiKey) {
        $this->validateAPIKey($apiKey);
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Check whether or not given key is valid
     *
     * @param $key
     * @throws HypixelPHPException
     */
    protected function validateAPIKey($key) {
        if ($key == null) {
            throw new HypixelPHPException("API Key can't be null!", ExceptionCodes::NO_KEY);
        } elseif (!Validator::isValidAPIKey($key)) {
            throw new HypixelPHPException("API Key is invalid!", ExceptionCodes::INVALID_KEY);
        }
    }

    /**
     * @param Logger $logger
     * @return $this
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return Logger
     */
    public function getLogger() {
        return $this->logger;
    }

    /**
     * @param Fetcher $fetcher
     * @return $this
     */
    public function setFetcher(Fetcher $fetcher) {
        $this->fetcher = $fetcher;
        return $this;
    }

    /**
     * @return Fetcher
     */
    public function getFetcher() {
        return $this->fetcher;
    }

    /**
     * @param CacheHandler $cacheHandler
     * @return $this
     */
    public function setCacheHandler(CacheHandler $cacheHandler) {
        $this->cacheHandler = $cacheHandler;
        return $this;
    }

    /**
     * @return CacheHandler
     */
    public function getCacheHandler() {
        return $this->cacheHandler;
    }

    /**
     * @param Provider $provider
     * @return $this
     */
    public function setProvider(Provider $provider) {
        $this->provider = $provider;
        return $this;
    }

    /**
     * @return Provider
     */
    public function getProvider() {
        return $this->provider;
    }

    /**
     * @param ResourceManager $resourceManager
     * @return $this
     */
    public function setResourceManager(ResourceManager $resourceManager) {
        $this->resourceManager = $resourceManager;
        return $this;
    }

    /**
     * @return ResourceManager
     */
    public function getResourceManager() {
        return $this->resourceManager;
    }

    /**
     * @param $pairs
     * @throws NoPairsException
     */
    protected function checkPairs($pairs) {
        if ($pairs == null || sizeof($pairs) == 0) {
            throw new NoPairsException();
        }
    }

    /**
     * @param $value
     * @return string|null
     */
    public function getUUIDFromVar($value) {
        switch (InputType::getType($value)) {
            case InputType::USERNAME:
                return $this->getUUID((string)$value);
            case InputType::UUID:
                return $value;
            case InputType::PLAYER_OBJECT:
                /** @var Player $value */
                return $value->getUUID();
        }
        return null;
    }

    /**
     * Function to get and cache UUID from username.
     * @param string $username
     *
     * @return string|null
     */
    public function getUUID($username) {
        $username = trim(strtolower((string)$username));
        $cached = $this->getCacheHandler()->getUUID($username);
        if ($cached != null) {
            return $cached;
        }

        if ($this->getCacheHandler()->getCacheTime(CacheTimes::UUID) == CacheHandler::MAX_CACHE_TIME) {
            // we're on max cache, ignore fetching
            // save a null value so we don't spam
            $obj = [
                'timestamp' => time(),
                'name_lowercase' => $username,
                'uuid' => null
            ];
            $this->getLogger()->log(LOG_DEBUG, "Failed getting UUID for '" . $username . "' saving null!");
            $this->getCacheHandler()->setPlayerUUID($username, $obj);
            return null;
        }

        {
            // try to use mojang
            try {
                $uuidURL = sprintf('https://api.mojang.com/users/profiles/minecraft/%s', $username);
                $response = $this->getFetcher()->getURLContents($uuidURL);
                if ($response->wasSuccessful()) {
                    if (isset($response->getData()['id'])) {
                        $obj = [
                            'timestamp' => time(),
                            'name_lowercase' => $username,
                            'uuid' => Utilities::ensureNoDashesUUID((string)$response->getData()['id'])
                        ];
                        $this->getLogger()->log(LOG_DEBUG, "Received UUID from Mojang for '" . $username . "': " . $obj['uuid']);
                        $this->getCacheHandler()->setPlayerUUID($username, $obj);
                        return $obj['uuid'];
                    }
                }
            } /** @noinspection PhpRedundantCatchClauseInspection */
            catch (BadResponseCodeException $e) {
                if ($e->getActualCode() == 429) {
                    // TODO exponential backoff
                } else if ($e->getActualCode() == 204) {
                    $obj = [
                        'timestamp' => time(),
                        'name_lowercase' => $username,
                        'uuid' => null
                    ];
                    $this->getLogger()->log(LOG_DEBUG, "Received empty content (doesn't exist) while getting UUID for '" . $username . "' saving null!");
                    $this->getCacheHandler()->setPlayerUUID($username, $obj);
                    return null;
                } else {
                    $this->getLogger()->log(LOG_ERR, $e);
                }
            }

            // if all else fails fall back to hypixel
            $response = $this->getFetcher()->fetch(FetchTypes::PLAYER, ['key' => $this->getAPIKey(), FetchParams::PLAYER_BY_NAME => $username]);
            if ($response->wasSuccessful()) {
                $obj = [
                    'timestamp' => time(),
                    'name_lowercase' => $username,
                    'uuid' => Utilities::ensureNoDashesUUID((string)$response->getData()['record']['uuid'])
                ];
                $this->getLogger()->log(LOG_DEBUG, "Received UUID from Hypixel for '" . $username . "': " . $obj['uuid']);
                $this->getCacheHandler()->setPlayerUUID($username, $obj);
                return $obj['uuid'];
            }
        }

        if ($this->getCacheHandler()->getCacheTime(CacheTimes::UUID) != CacheHandler::MAX_CACHE_TIME) {
            $this->getCacheHandler()->setCacheTime(CacheTimes::UUID, CacheHandler::MAX_CACHE_TIME);
            return $this->getUUID($username);
        }
        return null;
    }

    /**
     * @param array $pairs
     * @return null|Response|Player
     * @throws HypixelPHPException
     */
    public function getPlayer($pairs = []) {
        $this->checkPairs($pairs);

        foreach ($pairs as $key => $val) {
            if ($val == null || $val == '') continue;

            if ($key == FetchParams::PLAYER_BY_UNKNOWN || $key == FetchParams::PLAYER_BY_NAME) {
                return $this->getPlayer([FetchParams::PLAYER_BY_UUID => $this->getUUIDFromVar($val)]);
            } else if ($key == FetchParams::PLAYER_BY_UUID) {
                if (InputType::getType($val) !== InputType::UUID) {
                    throw new InvalidUUIDException($val);
                }
                $val = Utilities::ensureNoDashesUUID($val);

                return $this->handle(
                    $this->getCacheHandler()->getPlayer((string)$val),
                    function () use ($key, $val) {
                        return $this->getFetcher()->fetch(FetchTypes::PLAYER, ['key' => $this->getAPIKey(), $key => $val]);
                    },
                    $this->getProvider()->getPlayer()
                );
            }
        }
        return null;
    }

    /**
     * Handles cache expiry checks,
     * fetching new objects if needed and
     * loads cached extra data if applicable
     *
     * @param HypixelObject $cached
     * @param $responseSupplier
     * @param $constructor
     * @return HypixelObject|Response|null
     */
    protected function handle($cached, $responseSupplier, $constructor) {
        if ($cached instanceof HypixelObject && !$cached->isCacheExpired()) {
            return $cached;
        }

        try {
            $response = $responseSupplier();
            if ($response instanceof Response) {
                $data = $response->getData();

                // if there is no record, we assume it's null
                if ($response->wasSuccessful() && array_key_exists('record', $data)) {
                    $fetched = $constructor($this, $data);
                    if ($fetched instanceof HypixelObject) {
                        $fetched->handleNew($cached);
                        $fetched->save();

                        return $fetched;
                    }
                } else {
                    // fetch was not successful, attach response or
                    // return it so we can get the error
                    if ($cached != null) {
                        $cached->attachResponse($response);
                    } else {
                        return $response;
                    }
                }
            }
        } catch (Exception $exception) {
            $this->getLogger()->log(LOG_ERR, $exception->getMessage());
            $this->getLogger()->log(LOG_ERR, $exception->getTraceAsString());
        }

        return $cached;
    }

    /**
     * @param array $pairs
     * @return null|Response|Guild
     * @throws HypixelPHPException
     */
    public function getGuild($pairs = []) {
        $this->checkPairs($pairs);

        foreach ($pairs as $key => $val) {
            if ($val == null || $val == '') continue;

            if ($key == FetchParams::GUILD_BY_PLAYER_UNKNOWN || $key == FetchParams::GUILD_BY_PLAYER_NAME) {
                return $this->getGuild([FetchParams::GUILD_BY_PLAYER_UUID => $this->getUUIDFromVar($val)]);
            }

            if ($key == FetchParams::GUILD_BY_PLAYER_UUID) {
                if (InputType::getType($val) !== InputType::UUID) {
                    throw new InvalidUUIDException($val);
                }
                $val = Utilities::ensureNoDashesUUID($val);

                $id = $this->getCacheHandler()->getGuildIDForUUID($val);
                if ($id != null) {
                    if ($id instanceof Guild) {
                        return $id;
                    } else if (isset($id['guild'])) {
                        return $this->getGuild([FetchParams::GUILD_BY_ID => $id['guild']]);
                    } else if (is_string($id)) {
                        return $this->getGuild([FetchParams::GUILD_BY_ID => $id]);
                    } else {
                        return null;
                    }
                }

                $response = $this->getFetcher()->fetch(FetchTypes::FIND_GUILD, ['key' => $this->getAPIKey(), $key => $val]);
                if ($response->wasSuccessful()) {
                    $content = [
                        'timestamp' => time(),
                        'uuid' => $val,
                        'guild' => $response->getData()['guild']
                    ];

                    $this->getCacheHandler()->setGuildIDForUUID($val, $content);

                    return $this->getGuild([FetchParams::GUILD_BY_ID => $content['guild']]);
                }
            }

            if ($key == FetchParams::GUILD_BY_NAME) {
                $val = strtolower((string)$val);
                $id = $this->getCacheHandler()->getGuildIDForName($val);
                if ($id != null) {
                    if ($id instanceof Guild) {
                        return $id;
                    } else {
                        return $this->getGuild([FetchParams::GUILD_BY_ID => $id]);
                    }
                }

                $response = $this->getFetcher()->fetch(FetchTypes::FIND_GUILD, ['key' => $this->getAPIKey(), $key => $val]);
                if ($response->wasSuccessful()) {
                    $content = [
                        'timestamp' => time(),
                        'name_lower' => $val,
                        'guild' => $response->getData()['guild']
                    ];

                    $this->getCacheHandler()->setGuildIDForName($val, $content);

                    return $this->getGuild([FetchParams::GUILD_BY_ID => $content['guild']]);
                }
            }

            if ($key == FetchParams::GUILD_BY_ID) {
                return $this->handle(
                    $this->getCacheHandler()->getGuild((string)$val),
                    function () use ($key, $val) {
                        return $this->getFetcher()->fetch(FetchTypes::GUILD, ['key' => $this->getAPIKey(), $key => $val]);
                    },
                    $this->getProvider()->getGuild()
                );
            }
        }
        return null;
    }

    /**
     * @param array $pairs
     * @return null|Response|Status
     * @throws HypixelPHPException
     */
    public function getStatus($pairs = []) {
        $this->checkPairs($pairs);

        foreach ($pairs as $key => $val) {
            if ($val == null || $val == '') continue;

            if ($key == FetchParams::STATUS_BY_UUID) {
                if (InputType::getType($val) !== InputType::UUID) {
                    throw new InvalidUUIDException($val);
                }
                $val = Utilities::ensureNoDashesUUID($val);

                return $this->handle(
                    $this->getCacheHandler()->getStatus((string)$val),
                    function () use ($key, $val) {
                        return $this->getFetcher()->fetch(FetchTypes::STATUS, ['key' => $this->getAPIKey(), $key => $val]);
                    },
                    $this->getProvider()->getStatus()
                );
            }
        }
        return null;
    }

    /**
     * @param array $pairs
     * @return null|Response|RecentGames
     * @throws HypixelPHPException
     */
    public function getRecentGames($pairs = []) {
        $this->checkPairs($pairs);

        foreach ($pairs as $key => $val) {
            if ($val == null || $val == '') continue;

            if ($key == FetchParams::RECENT_GAMES_BY_UUID) {
                if (InputType::getType($val) !== InputType::UUID) {
                    throw new InvalidUUIDException($val);
                }
                $val = Utilities::ensureNoDashesUUID($val);

                return $this->handle(
                    $this->getCacheHandler()->getRecentGames((string)$val),
                    function () use ($key, $val) {
                        return $this->getFetcher()->fetch(FetchTypes::RECENT_GAMES, ['key' => $this->getAPIKey(), $key => $val]);
                    },
                    $this->getProvider()->getRecentGames()
                );
            }
        }
        return null;
    }

    /**
     * @param array $pairs
     * @return null|Response|Friends
     * @throws HypixelPHPException
     */
    public function getFriends($pairs = []) {
        $this->checkPairs($pairs);

        foreach ($pairs as $key => $val) {
            if ($val == null || $val == '') continue;

            if ($key == FetchParams::FRIENDS_BY_UUID) {
                if (InputType::getType($val) !== InputType::UUID) {
                    throw new InvalidUUIDException($val);
                }
                $val = Utilities::ensureNoDashesUUID($val);

                return $this->handle(
                    $this->getCacheHandler()->getFriends((string)$val),
                    function () use ($key, $val) {
                        return $this->getFetcher()->fetch(FetchTypes::FRIENDS, ['key' => $this->getAPIKey(), $key => $val]);
                    },
                    $this->getProvider()->getFriends()
                );
            }
        }
        return null;
    }

    /**
     * @return Boosters|Response|null
     */
    public function getBoosters() {
        return $this->handle(
            $this->getCacheHandler()->getBoosters(),
            function () {
                return $this->getFetcher()->fetch(FetchTypes::BOOSTERS, ['key' => $this->getAPIKey()]);
            },
            $this->getProvider()->getBoosters()
        );
    }

    /**
     * @return Leaderboards|Response|null
     */
    public function getLeaderboards() {
        return $this->handle(
            $this->getCacheHandler()->getLeaderboards(),
            function () {
                return $this->getFetcher()->fetch(FetchTypes::LEADERBOARDS, ['key' => $this->getAPIKey()]);
            },
            $this->getProvider()->getLeaderboards()
        );
    }

    /**
     * @return KeyInfo|Response|null
     */
    public function getKeyInfo() {
        return $this->handle(
            $this->getCacheHandler()->getKeyInfo($this->getAPIKey()),
            function () {
                return $this->getFetcher()->fetch(FetchTypes::KEY, ['key' => $this->getAPIKey()]);
            },
            $this->getProvider()->getKeyInfo()
        );
    }

    /**
     * @return PunishmentStats|Response|null
     */
    public function getPunishmentStats() {
        return $this->handle(
            $this->getCacheHandler()->getPunishmentStats(),
            function () {
                return $this->getFetcher()->fetch(FetchTypes::PUNISHMENT_STATS, ['key' => $this->getAPIKey()]);
            },
            $this->getProvider()->getPunishmentStats()
        );
    }

    /**
     * @return PlayerCount|Response|null
     */
    public function getPlayerCount() {
        return $this->handle(
            $this->getCacheHandler()->getPlayerCount(),
            function () {
                return $this->getFetcher()->fetch(FetchTypes::PLAYER_COUNT, ['key' => $this->getAPIKey()]);
            },
            $this->getProvider()->getPlayerCount()
        );
    }

    /**
     * @return GameCounts|Response|null
     */
    public function getGameCounts() {
        return $this->handle(
            $this->getCacheHandler()->getGameCounts(),
            function () {
                return $this->getFetcher()->fetch(FetchTypes::GAME_COUNTS, ['key' => $this->getAPIKey()]);
            },
            $this->getProvider()->getGameCounts()
        );
    }

    /**
     * @param $profile_id
     * @return SkyBlockProfile|Response|null
     */
    public function getSkyBlockProfile($profile_id) {
        return $this->handle(
            $this->getCacheHandler()->getSkyBlockProfile($profile_id),
            function () use ($profile_id) {
                return $this->getFetcher()->fetch(FetchTypes::SKYBLOCK_PROFILE, ['key' => $this->getAPIKey(), 'profile' => $profile_id]);
            },
            $this->getProvider()->getSkyBlockProfile()
        );
    }

    /**
     * @param $resource
     * @return Resource|Response|null
     */
    public function getResource($resource) {
        return $this->handle(
            $this->getCacheHandler()->getResource($resource),
            function () use ($resource) {
                return $this->getFetcher()->fetch(FetchTypes::RESOURCES . '/' . $resource);
            },
            function ($HypixelPHP, $data) use ($resource) {
                return new Resource($HypixelPHP, $data, $resource);
            }
        );
    }
}