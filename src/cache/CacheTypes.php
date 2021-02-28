<?php

namespace Plancke\HypixelPHP\cache;

/**
 * Class CacheTypes
 * @package Plancke\HypixelPHP\cache
 */
abstract class CacheTypes {

    const RESOURCES = 'resources';

    const PLAYERS = 'players';
    const PLAYER_UUID = 'player_uuid';

    const FRIENDS = 'friends';

    const GUILDS = 'guilds';
    const GUILDS_UUID = 'guilds_uuid';
    const GUILDS_NAME = 'guilds_name';

    const STATUS = 'status';
    const API_KEYS = 'api_keys';
    const RECENT_GAMES = 'recent_games';

    const SKYBLOCK_PROFILES = 'skyblock_profiles';

    const LEADERBOARDS = 'leaderboards';
    const BOOSTERS = 'boosters';
    const PUNISHMENT_STATS = 'punishmentStats';
    const PLAYER_COUNT = 'playerCount';
    const GAME_COUNTS = 'gameCounts';

}