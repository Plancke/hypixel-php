# Hypixel PHP

A PHP class for fetching Player/Guild information from the Public HypixelAPI

## Requirements
- PHP 5.4+
- Hypixel API key

## Usage

To interact with the API you need an API key, you can get a key by doing "/api" on the Hypixel Network.

```PHP
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
         'timeout' => 2000,
         'cache_folder_player' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player',
         'cache_folder_guild' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild',
         'cache_folder_friends' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/friends',
         'cache_folder_sessions' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/sessions',
         'cache_folder_keyInfo' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/keyInfo/',
         'cache_boosters' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/boosters.json',
         'cache_leaderboards' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/leaderboards.json',
         'log_folder' => $_SERVER['DOCUMENT_ROOT'] . '/logs/HypixelAPI',
         'logging' => true,
         'debug' => true,
         'use_curl' => true
    ]
);
```
     
All of these options are optional. They are provided above with their default values.

Once you have the API Object you can call the getter functions

```PHP
$player = $HypixelAPI->getPlayer(
    [
        KEYS::PLAYER_BY_NAME    => $NAME,
        KEYS::PLAYER_BY_UUID    => $UUID,
        KEYS::PLAYER_BY_UNKNOWN => $UUID_OR_NAME
    ]
);
```
```PHP
$guild = $HypixelAPI->getGuild(
    [
        'player'   => null,
        'byPlayer' => null,
        'byUuid'   => null,
        'byName'   => null,
        'id'       => null
    ]
);
$HypixelAPI->getPlayer($input)->getGuild();
```
```PHP
$session = $HypixelAPI->getSession(
    [
        'player' => null,
        'name' => null,
        'uuid' => null
    ]
);
$session = $HypixelAPI->getPlayer($input)->getSession();
```
```PHP
$friends = $HypixelAPI->getFriends(
    [
        'player' => null,
        'name' => null,
        'uuid' => null
    ]
);
$friends = $HypixelAPI->getPlayer($input)->getFriends();
```

# Hypixel GameInfo

The Game Info folder contains JSON files that have specific game items from the games that can be used in various locations.
If a game received an update/change we need to add data there.