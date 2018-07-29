<?php

namespace Plancke\HypixelPHP\responses\guild;

use Plancke\HypixelPHP\classes\APIObject;
use Plancke\HypixelPHP\HypixelPHP;

class GuildRanks extends APIObject {

    protected $ranks;
    protected $defaultRank;

    /**
     * @param HypixelPHP $HypixelPHP
     * @param $ranks
     */
    public function __construct(HypixelPHP $HypixelPHP, $ranks) {
        parent::__construct($HypixelPHP, $ranks);

        if (sizeof($ranks) == 0) {
            array_push($ranks, [
                'name' => 'MEMBER',
                'priority' => 1,
                'permissions' => []
            ]);
            array_push($ranks, [
                'name' => 'OFFICER',
                'priority' => 2,
                'permissions' => []
            ]);
        }
        array_push($ranks, [
            'name' => 'GUILDMASTER',
            'priority' => 9999999,
            'permissions' => [],
            'tag' => 'GM'
        ]);

        $this->ranks = [];
        foreach ($ranks as $rank) {
            $guildRank = new GuildRank($HypixelPHP, $rank);
            $this->ranks[strtolower($guildRank->getName())] = $guildRank;

            if ($guildRank->isDefault()) $this->defaultRank = $rank;
        }
    }

    /**
     * @return array[string]GuildRank
     */
    public function getRanks() {
        return $this->ranks;
    }

    /**
     * @param $rank
     * @return GuildRank
     */
    public function getRank($rank) {
        return $this->ranks[strtolower($rank)];
    }

    /**
     * @return GuildRank
     */
    public function getDefaultRank() {
        return $this->defaultRank;
    }
}