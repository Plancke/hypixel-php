<?php

namespace Plancke\HypixelPHP\cache;

/**
 * Class CacheTimes
 * @package Plancke\HypixelPHP\cache
 */
abstract class CacheTimes {

    const RESOURCES = 'resources';

    const UUID = 'uuid';
    const UUID_NOT_FOUND = 'uuidNotFound';
    const PLAYER = 'player';
    const GUILD = 'guild';
    const GUILD_NOT_FOUND = 'guildNotFound';
    const LEADERBOARDS = 'leaderboards';
    const PLAYER_COUNT = 'playerCount';
    const BOOSTERS = 'boosters';
    const SESSION = 'session';
    const KEY_INFO = 'keyInfo';
    const FRIENDS = 'friends';
    const WATCHDOG = 'watchdog';
    const GAME_COUNTS = 'gameCounts';
    const SKYBLOCK_PROFILE = 'skyblock_profile';

}