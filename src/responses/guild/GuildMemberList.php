<?php

namespace Plancke\HypixelPHP\responses\guild;

use Plancke\HypixelPHP\classes\APIObject;
use Plancke\HypixelPHP\HypixelPHP;

/**
 * Class GuildMemberList
 * @package Plancke\HypixelPHP\responses\guild
 */
class GuildMemberList extends APIObject {

    protected $list = [];
    protected $count;

    /**
     * @param HypixelPHP $HypixelPHP
     * @param Guild $guild
     */
    public function __construct(HypixelPHP $HypixelPHP, $guild) {
        parent::__construct($HypixelPHP, $guild->getArray('members'));

        $this->count = sizeof($this->data);

        $ranks = $guild->getRanks();
        $defaultRank = $ranks->getDefaultRank();
        foreach ($this->data as $player) {
            $rank = $player['rank'];
            if ($ranks->getRank($rank) == null) $rank = null;
            if ($rank == null && $defaultRank != null) $rank = $defaultRank->getName();
            $rank = strtolower($rank);
            if (!in_array($rank, array_keys($this->list))) $this->list[$rank] = [];
            array_push($this->list[$rank], new GuildMember($HypixelPHP, $guild, $player));
        }

        uksort($this->list, function ($k1, $k2) use ($ranks) {
            /** @var GuildRank $rank1 */
            $rank1 = $ranks->getRank($k1);
            /** @var GuildRank $rank2 */
            $rank2 = $ranks->getRank($k2);
            if ($rank1 == null) return -1;
            if ($rank2 == null) return 1;

            return $rank2->getPriority() <=> $rank1->getPriority();
        });
    }

    /**
     * @return array[string]GuildMember[]
     */
    public function getList() {
        return $this->list;
    }

    /**
     * @param $rank
     * @return GuildMember[]
     */
    public function getListByRank($rank) {
        return $this->list[strtolower($rank)];
    }


    /**
     * @return int
     */
    public function getMemberCount() {
        return sizeof($this->data);
    }
}