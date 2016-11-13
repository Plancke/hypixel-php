<?php

namespace Plancke\HypixelPHP\provider;

use Plancke\HypixelPHP\classes\Module;
use Plancke\HypixelPHP\responses\booster\Boosters;
use Plancke\HypixelPHP\responses\friend\Friends;
use Plancke\HypixelPHP\responses\guild\Guild;
use Plancke\HypixelPHP\responses\KeyInfo;
use Plancke\HypixelPHP\responses\Leaderboards;
use Plancke\HypixelPHP\responses\player\Player;
use Plancke\HypixelPHP\responses\Session;
use Plancke\HypixelPHP\responses\WatchdogStats;

class Provider extends Module {

    public function getPlayer() {
        return function ($HypixelPHP, $data) {
            return new Player($HypixelPHP, $data);
        };
    }

    public function getGuild() {
        return function ($HypixelPHP, $data) {
            return new Guild($HypixelPHP, $data);
        };
    }

    public function getSession() {
        return function ($HypixelPHP, $data) {
            return new Session($HypixelPHP, $data);
        };
    }

    public function getFriends() {
        return function ($HypixelPHP, $data) {
            return new Friends($HypixelPHP, $data);
        };
    }

    public function getBoosters() {
        return function ($HypixelPHP, $data) {
            return new Boosters($HypixelPHP, $data);
        };
    }

    public function getLeaderboards() {
        return function ($HypixelPHP, $data) {
            return new Leaderboards($HypixelPHP, $data);
        };
    }

    public function getKeyInfo() {
        return function ($HypixelPHP, $data) {
            return new KeyInfo($HypixelPHP, $data);
        };
    }

    public function getWatchdogStats() {
        return function ($HypixelPHP, $data) {
            return new WatchdogStats($HypixelPHP, $data);
        };
    }

}