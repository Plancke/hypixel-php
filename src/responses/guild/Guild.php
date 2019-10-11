<?php

namespace Plancke\HypixelPHP\responses\guild;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;
use Plancke\HypixelPHP\color\ColorUtils;
use Plancke\HypixelPHP\HypixelPHP;

class Guild extends HypixelObject {

    protected $ranks;
    protected $members;

    /**
     * @param HypixelPHP $HypixelPHP
     * @param $guild
     */
    public function __construct(HypixelPHP $HypixelPHP, $guild) {
        parent::__construct($HypixelPHP, $guild);

        $this->ranks = new GuildRanks($HypixelPHP, $this->getArray("ranks"));
        $this->members = new GuildMemberList($this->getHypixelPHP(), $this);
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->get('name');
    }

    /**
     * @return GuildRanks
     */
    public function getRanks() {
        return $this->ranks;
    }

    /**
     * @return string
     */
    public function getTag() {
        return $this->get('tag');
    }

    /**
     * @return string
     */
    public function getTagColor() {
        $color = $this->get('tagColor', 'GRAY');
        if (isset(ColorUtils::NAME_TO_CODE[$color])) {
            return ColorUtils::NAME_TO_CODE[$color];
        }
        return ColorUtils::GRAY;
    }

    /**
     * @return int
     */
    public function getMemberCount() {
        return $this->getMemberList()->getMemberCount();
    }

    /**
     * @return GuildMemberList
     */
    public function getMemberList() {
        return $this->members;
    }

    /**
     * @return string
     */
    function getCacheTimeKey() {
        return CacheTimes::GUILD;
    }

    /**
     * @return int
     */
    public function getLevel() {
        return GuildLevelUtil::getLevel($this->getExp());
    }

    /**
     * @return int
     */
    public function getExp() {
        return $this->getNumber('exp');
    }

    /**
     * @return array
     */
    public function getAchievements() {
        return $this->getArray("achievements");
    }

    /**
     * @return bool
     */
    public function isJoinable() {
        return $this->get('joinable', false);
    }

    /**
     * @return bool
     */
    public function isPubliclyListed() {
        return $this->get('publiclyListed', false);
    }

    /**
     * @return int
     */
    public function getLegacyRank() {
        return $this->getNumber('legacyRanking', -1);
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->get("description");
    }

    /**
     * @return array
     */
    public function getPreferredGames() {
        return $this->getArray("preferredGames");
    }

    /**
     * @return array
     */
    public function getBanner() {
        return $this->getArray("banner");
    }

    /**
     * @return array
     */
    public function getExpByGameType() {
        return $this->getArray("guildExpByGameType");
    }

    public function save() {
        $this->getHypixelPHP()->getCacheHandler()->setGuild($this);
    }
}