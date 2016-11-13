<?php

namespace Plancke\HypixelPHP\fetch;

use Plancke\HypixelPHP\classes\Module;

abstract class Fetcher extends Module {

    const BASE_URL = 'https://api.hypixel.net/';

    protected $timeOut;

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