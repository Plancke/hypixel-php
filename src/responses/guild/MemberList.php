<?php

namespace Plancke\HypixelPHP\responses\guild;

use Plancke\HypixelPHP\classes\APIHolding;
use Plancke\HypixelPHP\HypixelPHP;

/**
 * Class MemberList
 * @package Plancke\HypixelPHP\responses\guild
 */
class MemberList extends APIHolding {
    protected $list;
    protected $count;

    /**
     * @param HypixelPHP $HypixelPHP
     * @param $members
     */
    public function __construct(HypixelPHP $HypixelPHP, $members) {
        parent::__construct($HypixelPHP);

        $list = [
            "GUILDMASTER" => [],
            "OFFICER" => [],
            "MEMBER" => []
        ];

        $this->count = sizeof($members);
        foreach ($members as $player) {
            $rank = $player['rank'];
            if (!in_array($rank, array_keys($list))) {
                $list[$rank] = [];
            }

            $coinHistory = [];
            foreach ($player as $key => $val) {
                if (strpos($key, 'dailyCoins') !== false) {
                    $EXPLOSION = explode('-', $key);
                    $coinHistory[$EXPLOSION[1] . '-' . ($EXPLOSION[2] + 1) . '-' . $EXPLOSION[3]] = $val;
                    unset($player[$key]);
                }
            }
            $player['coinHistory'] = $coinHistory;

            array_push($list[$rank], new GuildMember($HypixelPHP, $player));
        }
        $this->list = $list;
    }

    /**
     * @return array[string]GuildMember
     */
    public function getList() {
        return $this->list;
    }

    /**
     * @return int
     */
    public function getMemberCount() {
        return $this->count;
    }
}