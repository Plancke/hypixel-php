<?php

namespace Plancke\HypixelPHP\responses\guild;

use Plancke\HypixelPHP\classes\APIObject;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\responses\player\Player;

class GuildMember extends APIObject {

    protected $rank;

    /**
     * @param HypixelPHP $HypixelPHP
     * @param Guild $guild
     * @param $member
     */
    public function __construct(HypixelPHP $HypixelPHP, Guild $guild, $member) {
        parent::__construct($HypixelPHP, $member);

        $this->rank = $guild->getRanks()->getRank($this->get("rank"));
    }

    /**
     * @return Player
     * @throws HypixelPHPException
     */
    public function getPlayer() {
        return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => $this->getUUID()]);
    }

    /**
     * @return string
     */
    public function getUUID() {
        return $this->get("uuid");
    }

    /**
     * @return GuildRank
     */
    public function getRank() {
        return $this->rank;
    }

    /**
     * @return int
     */
    public function getJoinTimeStamp() {
        return $this->getNumber("joined");
    }

    /**
     * @return int
     */
    public function getQuestParticipation() {
        return $this->getNumber("questParticipation");
    }

    /**
     * @return array[string]int
     */
    public function getExpHistory() {
        return $this->getArray("expHistory");
    }
}