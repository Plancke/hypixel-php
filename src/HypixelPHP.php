<?php
namespace Plancke\HypixelPHP;

use Closure;
use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\cache\impl\flat\FlatFileCacheHandler;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\exceptions\ExceptionCodes;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\Fetcher;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\fetch\FetchTypes;
use Plancke\HypixelPHP\fetch\impl\DefaultFetcher;
use Plancke\HypixelPHP\fetch\Response;
use Plancke\HypixelPHP\log\impl\DefaultLogger;
use Plancke\HypixelPHP\log\Logger;
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
 * @version 3.0.0
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
     * @param array $options
     * @throws \Exception
     */
    public function __construct($apiKey, $options = []) {
        $this->apiKey = $apiKey;
        $this->options = $options;

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
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Manually set option array
     *
     * @param $options
     * @return $this
     */
    public function _setOptions($options) {
        $this->options = $options;
        return $this;
    }

    /**
     * @param $input
     * @return $this
     */
    public function setOptions($input) {
        foreach ($input as $key => $val) {
            if ($this->options[$key] != $val) {
                if (is_array($val)) {
                    $this->getLogger()->log('Setting ' . $key . ' to ' . json_encode($val));
                } else {
                    $this->getLogger()->log('Setting ' . $key . ' to ' . $val);
                }
            }
            $this->options[$key] = $val;
        }
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
        $this->loggerGetter = function ($HypixelAPI) use ($logger) {
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
        $this->fetcherGetter = function ($HypixelAPI) use ($fetcher) {
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
        $this->cacheHandlerGetter = function ($HypixelAPI) use ($cacheHandler) {
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
        $this->providerGetter = function ($HypixelAPI) use ($provider) {
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
        $this->resourceManagerGetter = function ($HypixelAPI) use ($resourceManager) {
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
     * @return Player|Response|null
     */
    public function getPlayer($pairs = []) {
        foreach ($pairs as $key => $val) {
            if ($val == null || $val != '') continue;

            if ($key == FetchParams::PLAYER_BY_UNKNOWN || $key == FetchParams::PLAYER_BY_NAME) {
                return $this->getPlayer([FetchParams::PLAYER_BY_UUID => $this->getUUIDFromVar($val)]);
            }

            if ($key == FetchParams::PLAYER_BY_UUID) {
                if (InputType::getType($val) !== InputType::UUID) {
                    throw new HypixelPHPException("Input isn't a valid UUID", ExceptionCodes::INVALID_UUID);
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
            if (isset($response['id'])) {
                $obj = [
                    'timestamp' => time(),
                    'name_lowercase' => $username,
                    'uuid' => Utilities::ensureNoDashesUUID((string)$response['id'])
                ];
                $this->getLogger()->log("Received UUID from Mojang for '" . $username . "': " . $obj['uuid']);
                $this->getCacheHandler()->setPlayerUUID($username, $obj);
                return $obj['uuid'];
            }

            // if all else fails fall back to hypixel
            $response = $this->getFetcher()->fetch(FetchTypes::PLAYER, [FetchParams::PLAYER_BY_NAME => $username]);
            if ($response->wasSuccessful()) {
                $obj = [
                    'timestamp' => time(),
                    'name_lowercase' => $username,
                    'uuid' => Utilities::ensureNoDashesUUID((string)$response->getData()['uuid'])
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
     * @return Guild|Response|null
     */
    public function getGuild($pairs = []) {
        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') continue;

            if ($key == FetchParams::GUILD_BY_PLAYER_UNKNOWN || $key == FetchParams::GUILD_BY_PLAYER_NAME || $key == FetchParams::GUILD_BY_PLAYER_OBJECT) {
                return $this->getGuild([FetchParams::GUILD_BY_PLAYER_UUID => $this->getUUIDFromVar($val)]);
            }

            if ($key == FetchParams::GUILD_BY_PLAYER_UUID) {
                if (InputType::getType($val) !== InputType::UUID) {
                    throw new HypixelPHPException("Input isn't a valid UUID", ExceptionCodes::INVALID_UUID);
                }
                $val = Utilities::ensureNoDashesUUID($val);

                $id = $this->getCacheHandler()->getGuildIDForUUID($val);
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
     * @return Session|Response|null
     */
    public function getSession($pairs = []) {
        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') continue;

            if ($key == FetchParams::SESSION_BY_PLAYER_OBJECT) {
                return $this->getSession([FetchParams::SESSION_BY_UUID => $this->getUUIDFromVar($val)]);
            }

            if ($key == FetchParams::SESSION_BY_UUID) {
                if (InputType::getType($val) !== InputType::UUID) {
                    throw new HypixelPHPException("Input isn't a valid UUID", ExceptionCodes::INVALID_UUID);
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
     * @return Friends|Response|null
     */
    public function getFriends($pairs = []) {
        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') continue;

            if ($key == FetchParams::FRIENDS_BY_PLAYER_OBJECT) {
                return $this->getFriends([FetchParams::FRIENDS_BY_UUID => $this->getUUIDFromVar($val)]);
            }

            if ($key == FetchParams::FRIENDS_BY_UUID) {
                if (InputType::getType($val) !== InputType::UUID) {
                    throw new HypixelPHPException("Input isn't a valid UUID", ExceptionCodes::INVALID_UUID);
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
        if ($cached instanceof HypixelObject && !$cached->isCacheExpired()) {
            $this->getLogger()->log("Cached is still valid; returning cached");
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

                    $fetched->handleNew();

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