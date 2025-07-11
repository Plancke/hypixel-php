<?php

namespace Plancke\HypixelPHP\fetch;

use Closure;
use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\classes\Module;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\adapter\ResponseAdapter;
use Plancke\HypixelPHP\HypixelPHP;

/**
 * Class Fetcher
 * @package Plancke\HypixelPHP\fetch
 */
abstract class Fetcher extends Module {

    const BASE_URL = 'https://api.hypixel.net/v2/';

    protected $timeOut = 2000;
    protected $responseAdapter, $responseAdapterGetter;

    /**
     * Fetcher constructor.
     * @param HypixelPHP $HypixelPHP
     */
    public function __construct(HypixelPHP $HypixelPHP) {
        parent::__construct($HypixelPHP);

        $this->setResponseAdapterGetter(function ($HypixelPHP) {
            return new ResponseAdapter($HypixelPHP);
        });
    }

    /**
     * @param Closure $getter
     * @return $this
     */
    public function setResponseAdapterGetter(Closure $getter): Fetcher {
        $this->responseAdapterGetter = $getter;
        $this->responseAdapter = null;
        return $this;
    }

    /**
     * @return ResponseAdapter
     */
    public function getResponseAdapter(): ResponseAdapter {
        if ($this->responseAdapter == null) {
            $getter = $this->responseAdapterGetter;
            $this->responseAdapter = $getter($this->getHypixelPHP());
        }
        return $this->responseAdapter;
    }

    /**
     * @param ResponseAdapter $responseAdapter
     * @return $this
     */
    public function setResponseAdapter(ResponseAdapter $responseAdapter): Fetcher {
        $this->responseAdapter = $responseAdapter;
        $this->responseAdapterGetter = null;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeOut(): int {
        return $this->timeOut;
    }

    /**
     * @param int $timeOut
     * @return $this
     */
    public function setTimeOut(int $timeOut): Fetcher {
        $this->timeOut = $timeOut;
        return $this;
    }

    /**
     * @param string $fetch
     * @param array $keyValues
     * @return string
     */
    public function createUrl(string $fetch, $keyValues = []): string {
        $requestURL = Fetcher::BASE_URL . $fetch;
        if (sizeof($keyValues) > 0) {
            $requestURL .= '?';
            foreach ($keyValues as $key => $value) {
                $value = urlencode(trim($value));
                $requestURL .= '&' . $key . '=' . $value;
            }
        }
        return $requestURL;
    }

    /**
     * @param string $fetch
     * @param string $url
     * @param array $options
     * @return Response
     * @throws HypixelPHPException
     */
    public function fetch(string $fetch, string $url, $options = []): Response {
        if (!is_array($options)) {
            throw new HypixelPHPException("options is not an array");
        }

        $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Starting Fetch: ' . $url);

        $response = $this->getURLContents($url, $options);
        if (!$response->wasSuccessful()) {
            $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Fetch Failed! ' . var_export($response, true));

            // If one fails, stop trying for that status
            // ideally also have a cached check before
            $this->getHypixelPHP()->getCacheHandler()->setGlobalTime(CacheHandler::MAX_CACHE_TIME);
        } else {
            $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Fetch successful!');
        }

        return $this->getResponseAdapter()->adaptResponse($fetch, $response);
    }

    /**
     * @param string $url
     * @param array $options
     * @return Response
     */
    abstract function getURLContents(string $url, $options = []): Response;
}