<?php

namespace Plancke\HypixelPHP\cache\impl\mongo;

abstract class CollectionNames {
    const PLAYERS = 'players';
    const PLAYER_UUID = 'player_uuid';

    const FRIENDS = 'friends';

    const GUILDS = 'guilds';
    const GUILDS_UUID = 'guilds_uuid';
    const GUILDS_NAME = 'guilds_name';

    const SESSIONS = 'sessions';
    const API_KEYS = 'api_keys';

    // used for things that require just a single object to be saved
    const SINGLE_SAVE = 'single_save';
}