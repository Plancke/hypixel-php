<?php

namespace Plancke\HypixelPHP\fetch;

/**
 * Class FetchTypes
 * @package Plancke\HypixelPHP\fetch
 */
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
    const GAME_COUNTS = 'gameCounts';

    const SKYBLOCK_NEWS = 'skyblock/news';
    const SKYBLOCK_COLLECTIONS = 'skyblock/collections';
    const SKYBLOCK_SKILLS = 'skyblock/skills';
    const SKYBLOCK_PROFILE = 'skyblock/profile';

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
            self::GAME_COUNTS,
            self::SKYBLOCK_NEWS,
            self::SKYBLOCK_COLLECTIONS,
            self::SKYBLOCK_SKILLS,
            self::SKYBLOCK_PROFILE
        ];
    }

}