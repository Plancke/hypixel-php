<?php

namespace Plancke\HypixelPHP\fetch;

/**
 * Class FetchParams
 * @package Plancke\HypixelPHP\fetch
 */
abstract class FetchParams {
    const PLAYER_BY_NAME = 'name';
    const PLAYER_BY_UUID = 'uuid';
    const PLAYER_BY_UNKNOWN = 'unknown';

    const GUILD_BY_NAME = 'name';
    const GUILD_BY_PLAYER = 'player';
    /** @deprecated only supports uuids */
    const GUILD_BY_PLAYER_UUID = FetchParams::GUILD_BY_PLAYER;
    /** @deprecated only supports uuids */
    const GUILD_BY_PLAYER_NAME = FetchParams::GUILD_BY_PLAYER;
    /** @deprecated only supports uuids */
    const GUILD_BY_PLAYER_UNKNOWN = FetchParams::GUILD_BY_PLAYER;
    const GUILD_BY_ID = 'id';

    const STATUS_BY_UUID = 'uuid';
    const RECENT_GAMES_BY_UUID = 'uuid';

    /**
     * @return array
     */
    public static function values(): array {
        return [
            self::PLAYER_BY_NAME,
            self::PLAYER_BY_UUID,
            self::PLAYER_BY_UNKNOWN,

            self::GUILD_BY_NAME,
            self::GUILD_BY_PLAYER,
            self::GUILD_BY_PLAYER_UUID,
            self::GUILD_BY_PLAYER_NAME,
            self::GUILD_BY_PLAYER_UNKNOWN,
            self::GUILD_BY_ID,

            self::STATUS_BY_UUID,
            self::RECENT_GAMES_BY_UUID,
        ];
    }
}