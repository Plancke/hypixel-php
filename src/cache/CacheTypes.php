<?php

namespace Plancke\HypixelPHP\cache;

abstract class CacheTypes {
    const PLAYERS = 'players';
    const PLAYER_UUID = 'player_uuid';

    const FRIENDS = 'friends';

    const GUILDS = 'guilds';
    const GUILDS_UUID = 'guilds_uuid';
    const GUILDS_NAME = 'guilds_name';

    const SESSIONS = 'sessions';
    const API_KEYS = 'api_keys';

    // single saves
    const LEADERBOARDS = 'leaderboards';
    const BOOSTERS = 'boosters';
    const WATCHDOG_STATS = 'watchdogStats';
}