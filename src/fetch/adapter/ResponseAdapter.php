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
     * @param string $fetch
     * @param Response $response
     * @return Response
     * @throws HypixelPHPException
     */
    public function adaptResponse(string $fetch, Response $response): Response {
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
                return $this->wrapRecord($response->setData(['list' => $response->getData()['records'], 'uuid' => $response->getData()['uuid']]));
            case FetchTypes::SKYBLOCK_PROFILE:
                return $this->remapField('profile', $response);

            case FetchTypes::STATUS:
            case FetchTypes::PUNISHMENT_STATS:
            case FetchTypes::COUNTS:
            case FetchTypes::GAME_COUNTS:
            case FetchTypes::RECENT_GAMES:
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
    protected function wrapRecord(Response $response): Response {
        return $response->setData(['record' => $response->getData()]);
    }

    /**
     * @param $key
     * @param Response $response
     * @return Response
     */
    protected function remapField($key, Response $response): Response {
        if (!array_key_exists($key, $response->getData())) return $response;
        return $response->setData(['record' => $response->getData()[$key]]);
    }

}