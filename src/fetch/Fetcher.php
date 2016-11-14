<?php

namespace Plancke\HypixelPHP\fetch;

use Closure;
use Plancke\HypixelPHP\classes\Module;
use Plancke\HypixelPHP\fetch\adapter\ResponseAdapter;
use Plancke\HypixelPHP\HypixelPHP;

abstract class Fetcher extends Module {

    const BASE_URL = 'https://api.hypixel.net/';

    protected $timeOut;
    private $responseAdapter, $responseAdapterGetter;

    public function __construct(HypixelPHP $HypixelPHP) {
        parent::__construct($HypixelPHP);

        $this->setResponseAdapterGetter(function ($HypixelPHP) {
            return new ResponseAdapter($HypixelPHP);
        });
    }

    /**
     * @return int
     */
    public function getTimeOut() {
        return $this->timeOut;
    }

    /**
     * @param int $timeOut
     * @return $this
     */
    public function setTimeOut($timeOut) {
        $this->timeOut = $timeOut;
        return $this;
    }

    /**
     * @return ResponseAdapter
     */
    public function getResponseAdapter() {
        if ($this->responseAdapter == null) {
            $getter = $this->responseAdapterGetter;
            $this->responseAdapter = $getter($this->getHypixelPHP());
        }
        return $this->responseAdapter;
    }

    /**
     * @param ResponseAdapter $logger
     * @return $this
     */
    public function setResponseAdapter(ResponseAdapter $logger) {
        $this->responseAdapter = $logger;
        $this->responseAdapterGetter = function ($HypixelAPI) use ($logger) {
            return $logger;
        };
        return $this;
    }

    /**
     * @param Closure $getter
     * @return $this
     */
    public function setResponseAdapterGetter(Closure $getter) {
        $this->responseAdapterGetter = $getter;
        $this->responseAdapter = null;
        return $this;
    }

    /**
     * @param $fetch
     * @param array $keyValues
     * @return Response
     */
    abstract function fetch($fetch, $keyValues = []);

    /**
     * @param $url
     * @return Response
     */
    abstract function getURLContents($url);
}