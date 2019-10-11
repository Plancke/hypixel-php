<?php

namespace Plancke\HypixelPHP\fetch;

use Closure;
use Plancke\HypixelPHP\classes\Module;
use Plancke\HypixelPHP\fetch\adapter\ResponseAdapter;
use Plancke\HypixelPHP\HypixelPHP;

/**
 * Class Fetcher
 * @package Plancke\HypixelPHP\fetch
 */
abstract class Fetcher extends Module {

    const BASE_URL = 'https://api.hypixel.net/';

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
    public function setResponseAdapterGetter(Closure $getter) {
        $this->responseAdapterGetter = $getter;
        $this->responseAdapter = null;
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
     * @param ResponseAdapter $responseAdapter
     * @return $this
     */
    public function setResponseAdapter(ResponseAdapter $responseAdapter) {
        $this->responseAdapter = $responseAdapter;
        $this->responseAdapterGetter = null;
        return $this;
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
     * @param string $fetch
     * @param array $keyValues
     * @return Response
     */
    abstract function fetch($fetch, $keyValues = []);

    /**
     * @param string $url
     * @return Response
     */
    abstract function getURLContents($url);
}