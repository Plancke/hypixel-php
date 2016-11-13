<?php

namespace Plancke\HypixelPHP\cache\impl\mongo;

use Plancke\HypixelPHP\HypixelPHP;
use MongoClient;

/**
 * Implementation for CacheHandler, stores data in MongoDB
 *
 * Class MongoCacheHandler
 * @package HypixelPHP
 */
class MongoCacheHandler extends AMongoCacheHandler  {

    protected $mongoClient;

    public function __construct(HypixelPHP $HypixelPHP, MongoClient $mongoClient) {
        parent::__construct($HypixelPHP);

        $this->mongoClient = $mongoClient;
    }

    /**
     * @return \MongoDB
     */
    public function selectDB() {
        return $this->mongoClient->selectDB("HypixelPHP");
    }

}