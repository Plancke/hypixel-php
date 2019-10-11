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
     * @param $fetch
     * @param $keyValues
     * @param Response $response
     * @return Response
     * @throws HypixelPHPException
     */
    public function adaptResponse($fetch, $keyValues, Response $response) {
        if (strpos($fetch, FetchTypes::RESOURCES) === 0) {
            return $this->wrapRecord($response);
        }

        switch ($fetch) {
            case FetchTypes::PLAYER:
                return $this->remapField('player', $response);
            case FetchTypes::GUILD:
                return $this->remapField('guild', $response);
            case FetchTypes::BOOSTERS:
                return $this->remapField('boosters', $response);
            case FetchTypes::LEADERBOARDS:
                return $this->remapField('leaderboards', $response);
            case FetchTypes::FRIENDS:
                return $this->attachKeyValues($keyValues, $this->wrapRecord($response->setData(['list' => $response->getData()['records']])));
            case FetchTypes::SESSION:
                return $this->attachKeyValues($keyValues, $this->remapField('session', $response));
            case FetchTypes::SKYBLOCK_PROFILE:
                return $this->remapField('profile', $response);

            case FetchTypes::WATCHDOG_STATS:
            case FetchTypes::PLAYER_COUNT:
            case FetchTypes::GAME_COUNTS:
                return $this->wrapRecord($response);

            case FetchTypes::KEY:
            case FetchTypes::FIND_GUILD:
                return $response;

            default:
                throw new HypixelPHPException("Invalid Adapter Key: " . $fetch, ExceptionCodes::INVALID_ADAPTER_KEY);
        }
    }

    /**
     * @param Response $response
     * @return Response
     */
    protected function wrapRecord(Response $response) {
        return $response->setData(['record' => $response->getData()]);
    }

    /**
     * @param $key
     * @param Response $response
     * @return Response
     */
    protected function remapField($key, Response $response) {
        if (!array_key_exists($key, $response->getData())) return $response;
        return $response->setData(['record' => $response->getData()[$key]]);
    }

    /**
     * @param $keyValues
     * @param Response $response
     * @return Response
     */
    protected function attachKeyValues($keyValues, Response $response) {
        $data = $response->getData();
        if (array_key_exists('record', $data) && is_array($data['record'])) {
            $data['record'] = array_merge($data['record'], $keyValues);
        }
        return $response->setData($data);
    }
}