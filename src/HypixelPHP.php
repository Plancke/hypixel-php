<?php
namespace Plancke\HypixelPHP;

use Closure;
use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\cache\impl\FlatFileCacheHandler;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\exceptions\ExceptionCodes;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\exceptions\InvalidUUIDException;
use Plancke\HypixelPHP\exceptions\NoPairsException;
use Plancke\HypixelPHP\fetch\Fetcher;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\fetch\FetchTypes;
use Plancke\HypixelPHP\fetch\impl\DefaultFetcher;
use Plancke\HypixelPHP\fetch\Response;
use Plancke\HypixelPHP\log\impl\DefaultLogger;
use Plancke\HypixelPHP\log\Logger;
use Plancke\HypixelPHP\options\Options;
use Plancke\HypixelPHP\provider\Provider;
use Plancke\HypixelPHP\resources\ResourceManager;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\responses\WatchdogStats;
use Plancke\HypixelPHP\util\InputType;
use Plancke\HypixelPHP\util\Utilities;

/**
 * HypixelPHP
 *
 * @author Plancke
 * @link https://plancke.io
 *
 */
class HypixelPHP {

    private $apiKey;
    private $options;

    private $logger, $loggerGetter;
    private $fetcher, $fetcherGetter;
    private $cacheHandler, $cacheHandlerGetter;
    private $provider, $providerGetter;
    private $resourceManager, $resourceManagerGetter;

    /**
     * @param string $apiKey
     * @throws HypixelPHPException
     */
    public function __construct($apiKey) {
        $this->apiKey = $apiKey;

        if ($this->apiKey == null) {
            throw new HypixelPHPException("API Key can't be null!", ExceptionCodes::NO_KEY);
        } elseif (InputType::getType($this->apiKey) !== InputType::UUID) {
            throw new HypixelPHPException("API Key is invalid!", ExceptionCodes::INVALID_KEY);
        }

        $this->setLoggerGetter(function ($HypixelPHP) {
            return new DefaultLogger($HypixelPHP);
        });
        $this->setFetcherGetter(function ($HypixelPHP) {
            return new DefaultFetcher($HypixelPHP);
        });
        $this->setCacheHandlerGetter(function ($HypixelPHP) {
            return new FlatFileCacheHandler($HypixelPHP);
        });
        $this->setProviderGetter(function ($HypixelPHP) {
            return new Provider($HypixelPHP);
        });
        $this->setResourceManagerGetter(function ($HypixelPHP) {
            return new ResourceManager($HypixelPHP);
        });
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
     */
    public function setAPIKey($apiKey) {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return Options
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions(Options $options) {
        $this->options = $options;
        return $this;
    }

    /**
     * @return Logger
     */
    public function getLogger() {
        if ($this->logger == null) {
            $getter = $this->loggerGetter;
            $this->logger = $getter($this);
        }
        return $this->logger;
    }

    /**
     * @param Logger $logger
     * @return $this
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
        $this->loggerGetter = function () use ($logger) {
            return $logger;
        };
        return $this;
    }

    /**
     * @param Closure $getter
     * @return $this
     */
    public function setLoggerGetter(Closure $getter) {
        $this->loggerGetter = $getter;
        $this->logger = null;
        return $this;
    }

    /**
     * @return Fetcher
     */
    public function getFetcher() {
        if ($this->fetcher == null) {
            $getter = $this->fetcherGetter;
            $this->fetcher = $getter($this);
        }
        return $this->fetcher;
    }

    /**
     * @param Fetcher $fetcher
     * @return $this
     */
    public function setFetcher(Fetcher $fetcher) {
        $this->fetcher = $fetcher;
        $this->fetcherGetter = function () use ($fetcher) {
            return $fetcher;
        };
        return $this;
    }

    /**
     * @param Closure $getter
     * @return $this
     */
    public function setFetcherGetter(Closure $getter) {
        $this->fetcherGetter = $getter;
        $this->fetcher = null;
        return $this;
    }

    /**
     * @return CacheHandler
     */
    public function getCacheHandler() {
        if ($this->cacheHandler == null) {
            $getter = $this->cacheHandlerGetter;
            $this->cacheHandler = $getter($this);
        }
        return $this->cacheHandler;
    }

    /**
     * @param CacheHandler $cacheHandler
     * @return $this
     */
    public function setCacheHandler(CacheHandler $cacheHandler) {
        $this->cacheHandler = $cacheHandler;
        $this->cacheHandlerGetter = function ($HypixelPHP) use ($cacheHandler) {
            return $cacheHandler;
        };
        return $this;
    }

    /**
     * @param Closure $getter
     * @return $this
     */
    public function setCacheHandlerGetter(Closure $getter) {
        $this->cacheHandlerGetter = $getter;
        $this->cacheHandler = null;
        return $this;
    }

    /**
     * @return Provider
     */
    public function getProvider() {
        if ($this->provider == null) {
            $getter = $this->providerGetter;
            $this->provider = $getter($this);
        }
        return $this->provider;
    }

    /**
     * @param Provider $provider
     * @return $this
     */
    public function setProvider(Provider $provider) {
        $this->provider = $provider;
        $this->providerGetter = function () use ($provider) {
            return $provider;
        };
        return $this;
    }

    /**
     * @param Closure $getter
     * @return $this
     */
    public function setProviderGetter(Closure $getter) {
        $this->providerGetter = $getter;
        $this->provider = null;
        return $this;
    }

    /**
     * @return ResourceManager
     */
    public function getResourceManager() {
        if ($this->resourceManager == null) {
            $getter = $this->resourceManagerGetter;
            $this->resourceManager = $getter($this);
        }
        return $this->resourceManager;
    }

    /**
     * @param ResourceManager $resourceManager
     * @return $this
     */
    public function setResourceManager(ResourceManager $resourceManager) {
        $this->resourceManager = $resourceManager;
        $this->resourceManagerGetter = function () use ($resourceManager) {
            return $resourceManager;
        };
        return $this;
    }

    /**
     * @param Closure $getter
     * @return $this
     */
    public function setResourceManagerGetter(Closure $getter) {
        $this->resourceManagerGetter = $getter;
        $this->resourceManager = null;
        return $this;
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
            }

            if ($key == FetchParams::PLAYER_BY_UUID) {
                if (InputType::getType($val) !== InputType::UUID) {
                    throw new InvalidUUIDException();
                }
                $val = Utilities::ensureNoDashesUUID($val);

                return $this->handle(
                    $this->getCacheHandler()->getCachedPlayer((string)$val),
                    function () use ($key, $val) {
                        return $this->getFetcher()->fetch(FetchTypes::PLAYER, [$key => $val]);
                    },
                    $this->getProvider()->getPlayer()
                );
            }
        }
        return null;
    }

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
        $username = strtolower((string)$username);
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
            $this->getLogger()->log("Failed getting UUID for '" . $username . "' saving null!");
            $this->getCacheHandler()->setPlayerUUID($username, $obj);
            return null;
        }

        {
            // try to use mojang
            $uuidURL = sprintf('https://api.mojang.com/users/profiles/minecraft/%s', $username);
            $response = $this->getFetcher()->getURLContents($uuidURL);
            if ($response->wasSuccessful()) {
                if (isset($response->getData()['id'])) {
                    $obj = [
                        'timestamp' => time(),
                        'name_lowercase' => $username,
                        'uuid' => Utilities::ensureNoDashesUUID((string)$response->getData()['id'])
                    ];
                    $this->getLogger()->log("Received UUID from Mojang for '" . $username . "': " . $obj['uuid']);
                    $this->getCacheHandler()->setPlayerUUID($username, $obj);
                    return $obj['uuid'];
                }
            }

            // if all else fails fall back to hypixel
            $response = $this->getFetcher()->fetch(FetchTypes::PLAYER, [FetchParams::PLAYER_BY_NAME => $username]);
            if ($response->wasSuccessful()) {
                $obj = [
                    'timestamp' => time(),
                    'name_lowercase' => $username,
                    'uuid' => Utilities::ensureNoDashesUUID((string)$response->getData()['record']['uuid'])
                ];
                $this->getLogger()->log("Received UUID from Hypixel for '" . $username . "': " . $obj['uuid']);
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
                    throw new InvalidUUIDException();
                }
                $val = Utilities::ensureNoDashesUUID($val);

                $id = $this->getCacheHandler()->getGuildIDForUUID($val);
                if ($id != null) {
                    if ($id instanceof Guild) {
                        return $id;
                    } else if (isset($id['guild'])) {
                        return $this->getGuild([FetchParams::GUILD_BY_ID => $id['guild']]);
                    } else {
                        return null;
                    }
                }

                $response = $this->getFetcher()->fetch(FetchTypes::FIND_GUILD, [$key => $val]);
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

                $response = $this->getFetcher()->fetch(FetchTypes::FIND_GUILD, [$key => $val]);
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
                    $this->getCacheHandler()->getCachedGuild((string)$val),
                    function () use ($key, $val) {
                        return $this->getFetcher()->fetch(FetchTypes::GUILD, [$key => $val]);
                    },
                    $this->getProvider()->getGuild()
                );
            }
        }
        return null;
    }

    /**
     * @param array $pairs
     * @return null|Response|Session
     * @throws HypixelPHPException
     */
    public function getSession($pairs = []) {
        $this->checkPairs($pairs);

        foreach ($pairs as $key => $val) {
            if ($val == null || $val == '') continue;

            if ($key == FetchParams::SESSION_BY_UUID) {
                if (InputType::getType($val) !== InputType::UUID) {
                    throw new InvalidUUIDException();
                }
                $val = Utilities::ensureNoDashesUUID($val);

                return $this->handle(
                    $this->getCacheHandler()->getCachedSession((string)$val),
                    function () use ($key, $val) {
                        return $this->getFetcher()->fetch(FetchTypes::SESSION, [$key => $val]);
                    },
                    $this->getProvider()->getSession()
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
                    throw new InvalidUUIDException();
                }
                $val = Utilities::ensureNoDashesUUID($val);

                return $this->handle(
                    $this->getCacheHandler()->getCachedFriends((string)$val),
                    function () use ($key, $val) {
                        return $this->getFetcher()->fetch(FetchTypes::FRIENDS, [$key => $val]);
                    },
                    $this->getProvider()->getFriends()
                );
            }
        }
        return null;
    }

    private function checkPairs($pairs) {
        if ($pairs == null || sizeof($pairs) == 0) {
            throw new NoPairsException();
        }
    }

    /**
     * @return Boosters|Response|null
     */
    public function getBoosters() {
        return $this->handle(
            $this->getCacheHandler()->getCachedBoosters(),
            function () {
                return $this->getFetcher()->fetch(FetchTypes::BOOSTERS);
            },
            $this->getProvider()->getBoosters()
        );
    }

    /**
     * @return Leaderboards|Response|null
     */
    public function getLeaderboards() {
        return $this->handle(
            $this->getCacheHandler()->getCachedLeaderboards(),
            function () {
                return $this->getFetcher()->fetch(FetchTypes::LEADERBOARDS);
            },
            $this->getProvider()->getLeaderboards()
        );
    }

    /**
     * @return KeyInfo|Response|null
     */
    public function getKeyInfo() {
        return $this->handle(
            $this->getCacheHandler()->getCachedKeyInfo($this->getAPIKey()),
            function () {
                return $this->getFetcher()->fetch(FetchTypes::KEY);
            },
            $this->getProvider()->getKeyInfo()
        );
    }

    /**
     * @return WatchdogStats|Response|null
     */
    public function getWatchdogStats() {
        return $this->handle(
            $this->getCacheHandler()->getCachedWatchdogStats(),
            function () {
                return $this->getFetcher()->fetch(FetchTypes::WATCHDOG_STATS);
            },
            $this->getProvider()->getWatchdogStats()
        );
    }

    /**
     * Handles cache expiry checks,
     * fetching new objects if needed and
     * loads cached extra data if applicable
     *
     * @param $responseSupplier
     * @param $constructor
     * @param HypixelObject $cached
     *
     * @return HypixelObject|Response|null
     */
    private function handle($cached, $responseSupplier, $constructor) {
        $this->getLogger()->log("Handling; " . get_class($cached));
        if ($cached instanceof HypixelObject && !$cached->isCacheExpired()) {
            $this->getLogger()->log("Cached valid");
            return $cached;
        }

        $response = $responseSupplier();
        if ($response instanceof Response) {
            if ($response->wasSuccessful()) {
                $data = $response->getData();
                if (!array_key_exists('record', $data)) {
                    $data = ['record' => $data];
                }
                $fetched = $constructor($this, $data);
                if ($fetched instanceof HypixelObject) {
                    if ($cached instanceof HypixelObject) {
                        // update with cached extra, only locally
                        // since we are already saving the whole thing later
                        $fetched->_setExtra($cached->getExtra());
                    }

                    $fetched->handleNew($cached);

                    $this->getCacheHandler()->_setCache($fetched);

                    return $fetched;
                }
            } else {
                // fetch was not successful, attach response or
                // return it so we can get the error
                if ($cached != null) {
                    $this->getLogger()->log("Attaching response");
                    $cached->attachResponse($response);
                } else {
                    return $response;
                }
            }
        }

        return $cached;
    }

    /**
     * @param $in
     * @return HypixelObject|null
     */
    public function ignoreResponse($in) {
        if ($in instanceof HypixelObject) {
            return $in;
        }
        return null;
    }

    /**
     * @param $in
     * @return Response|null
     */
    public function getResponse($in) {
        if ($in instanceof HypixelObject) {
            return $in->getResponse();
        } else if ($in instanceof Response) {
            return $in;
        }
        return null;
    }
}