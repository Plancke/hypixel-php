<?php

namespace Plancke\HypixelPHP\fetch;

abstract class FetchParams {
    const PLAYER_BY_NAME = 'name';
    const PLAYER_BY_UUID = 'uuid';
    const PLAYER_BY_UNKNOWN = 'unknown';

    const GUILD_BY_NAME = 'byName';
    const GUILD_BY_PLAYER_UUID = 'byUuid';
    const GUILD_BY_PLAYER_NAME = 'byPlayer';
    const GUILD_BY_PLAYER_UNKNOWN = 'playerUnknown';
    const GUILD_BY_ID = 'id';

    const FRIENDS_BY_UUID = 'uuid';

    const SESSION_BY_UUID = 'uuid';
}