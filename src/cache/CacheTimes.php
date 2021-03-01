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
    const BOOSTERS = 'boosters';
    const STATUS = 'status';
    const RECENT_GAMES = 'recentGames';
    const KEY_INFO = 'keyInfo';
    const FRIENDS = 'friends';
    const PUNISHMENT_STATS = 'punishmentStats';
    const COUNTS = 'counts';
    const SKYBLOCK_PROFILE = 'skyblock_profile';

}