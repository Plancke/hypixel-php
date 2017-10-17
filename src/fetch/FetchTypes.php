<?php

namespace Plancke\HypixelPHP\fetch;

abstract class FetchTypes {

    const PLAYER = 'player';

    const GUILD = 'guild';
    const FIND_GUILD = 'findGuild';

    const FRIENDS = 'friends';
    const BOOSTERS = 'boosters';
    const LEADERBOARDS = 'leaderboards';
    const SESSION = 'session';
    const KEY = 'key';
    const WATCHDOG_STATS = 'watchdogStats';
    const PLAYER_COUNT = 'playerCount';

    public static function values() {
        return [
            self::PLAYER,
            self::GUILD,
            self::FIND_GUILD,
            self::BOOSTERS,
            self::LEADERBOARDS,
            self::SESSION,
            self::KEY,
            self::WATCHDOG_STATS,
            self::PLAYER_COUNT,
        ];
    }

}