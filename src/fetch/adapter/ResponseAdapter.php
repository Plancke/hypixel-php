<?php

namespace Plancke\HypixelPHP\fetch\adapter;

use Plancke\HypixelPHP\classes\Module;
use Plancke\HypixelPHP\exceptions\ExceptionCodes;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\FetchTypes;
use Plancke\HypixelPHP\fetch\Response;

/**
 * Class ResponseAdapter
 * @package Plancke\HypixelPHP\fetch\adapter
 */
class ResponseAdapter extends Module {

    /**
     * @param $key
     * @param Response $response
     * @return Response
     * @throws HypixelPHPException
     */
    public function adaptResponse($key, Response $response) {
        switch ($key) {
            case FetchTypes::PLAYER:
                return $this->remapField('player', $response);
            case FetchTypes::GUILD:
                return $this->remapField('guild', $response);
            case FetchTypes::FRIENDS:
                return $this->remapField('records', $response);
            case FetchTypes::BOOSTERS:
                return $this->remapField('boosters', $response);
            case FetchTypes::LEADERBOARDS:
                return $this->remapField('leaderboards', $response);
            case FetchTypes::SESSION:
                return $this->remapField('session', $response);

            case FetchTypes::PLAYER_COUNT:
            case FetchTypes::WATCHDOG_STATS:
                return $this->wrapRecord($response);

            case FetchTypes::KEY:
            case FetchTypes::FIND_GUILD:
                return $response;

            default:
                throw new HypixelPHPException("Invalid Adapter Key: " . $key, ExceptionCodes::INVALID_ADAPTER_KEY);
        }
    }

    /**
     * @param $key
     * @param Response $response
     * @return Response
     */
    private function remapField($key, Response $response) {
        return $response->setData(['record' => $response->getData()[$key]]);
    }

    /**
     * @param Response $response
     * @return Response
     */
    private function wrapRecord(Response $response) {
        return $response->setData(['record' => $response->getData()]);
    }
}