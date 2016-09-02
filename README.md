# Hypixel PHP

A PHP class for fetching Player/Guild information from the Public HypixelAPI
Documentation for the Public API can be found here: https://github.com/HypixelDev/PublicAPI

## Requirements
- PHP 5.4+
- Hypixel API key

## Usage

To interact with the API you need an API key, you can get a key by doing "/api" on the Hypixel Network.
Construct a HypixelAPI object, all options except for the api_key are optional.

```PHP
// HypixelPHP
$HypixelAPI = new HypixelAPI(
    [
        'api_key' => '',
        'cache_times' => [
            CACHE_TIMES::OVERALL => 600,
            CACHE_TIMES::PLAYER => 600,
            CACHE_TIMES::UUID => 864000,
            CACHE_TIMES::UUID_NOT_FOUND => 600,
            CACHE_TIMES::GUILD => 600,
            CACHE_TIMES::GUILD_NOT_FOUND => 600,
        ],
        'timeout' => 1000,
        'cache_folder_player' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player',
        'cache_folder_guild' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild',
        'cache_folder_friends' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/friends',
        'cache_folder_sessions' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/sessions',
        'cache_folder_keyInfo' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/keyInfo/',
        'cache_boosters' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/boosters.json',
        'cache_leaderboards' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/leaderboards.json',
        'log_folder' => $_SERVER['DOCUMENT_ROOT'] . '/logs/HypixelAPI',
        'achievements_file' => $_SERVER['DOCUMENT_ROOT'] . '/assets/achievements.json',
        'logging' => true,
        'debug' => true,
        'use_curl' => true
    ]
);

// HypixelPHP_Mongo
$HypixelAPI = new HypixelAPI(
    [
        'api_key' => '',
        'cache_times' => [
            CACHE_TIMES::OVERALL => 600,
            CACHE_TIMES::PLAYER => 600,
            CACHE_TIMES::UUID => 864000,
            CACHE_TIMES::UUID_NOT_FOUND => 600,
            CACHE_TIMES::GUILD => 600,
            CACHE_TIMES::GUILD_NOT_FOUND => 600,
        ],
        'timeout' => 1000,
        'log_folder' => $_SERVER['DOCUMENT_ROOT'] . '/logs/HypixelAPI',
        'cache_boosters' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/boosters.json',
        'cache_leaderboards' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/leaderboards.json',
        'achievements_file' => $_SERVER['DOCUMENT_ROOT'] . '/assets/achievements.json',
        'logging' => false,
        'debug' => false,
        'use_curl' => true
    ]
);
```

### Examples

Examples can be found at HypixelPHP/examples

## Game Info
The [Game Info](https://github.com/Plancke/hypixel-php/tree/master/game_info) directory contains JSON files that have specific game items from the games that can be used in various locations.