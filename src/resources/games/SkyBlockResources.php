<?php

namespace Plancke\HypixelPHP\resources\games;

use Plancke\HypixelPHP\resources\Resources;
use Plancke\HypixelPHP\responses\Resource;

class SkyBlockResources extends Resources {

    /**
     * @return Resource
     */
    public function getNews() {
        return $this->requireRemoteResourceFile('skyblock/news');
    }

    /**
     * @return Resource
     */
    public function getSkills() {
        return $this->requireRemoteResourceFile('skyblock/skills');
    }

    /**
     * @return Resource
     */
    public function getCollections() {
        return $this->requireRemoteResourceFile('skyblock/collections');
    }

}