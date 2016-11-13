<?php

namespace Plancke\HypixelPHP\cache\impl\mongo;

use MongoDB\Client;
use Plancke\HypixelPHP\HypixelPHP;

/**
 * Implementation for CacheHandler, stores data in MongoDB
 * Used for PHP7 because it deprecated MongoClient
 *
 * Class MongoCacheHandlerSeven
 * @package HypixelPHP
 */
class MongoCacheHandlerSeven extends AMongoCacheHandler {

    protected $mongoClient;

    public function __construct(HypixelPHP $HypixelPHP, Client $mongoClient) {
        parent::__construct($HypixelPHP);

        $this->mongoClient = $mongoClient;
    }

    /**
     * @return \MongoDB
     */
    public function selectDB() {
        return $this->mongoClient->selectDatabase("HypixelPHP");
    }

}