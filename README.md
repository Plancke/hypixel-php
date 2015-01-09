# Hypixel PHP

A PHP class for fetching Player/Guild information from the Public HypixelAPI

## Usage

To interact with the API you need an API key, you can get a key by doing "/api" on the Hypixel Network.

```PHP
$HypixelAPI = new HypixelAPI(array(
    'api_key' => '',
    'cache_time' => 600,
    'cache_folder_player' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player',
    'cache_folder_guild' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild',
    'cache_folder_friends' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/friends',
    'cache_folder_sessions' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/sessions',
    'cache_boosters' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/boosters.json',
    'cache_leaderboards' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/leaderboards.json',
    'debug' => false,
    'use_curl' => true
 ));
 ```
     
All of these options are optional. They are provided above with their default values.

Once you have the API Object you can call the getter functions

```PHP
$player = $HypixelAPI->getPlayer(array(
    'name' => '',
    'uuid' => ''
));

$guild = $HypixelAPI->getGuild(array(
    'byPlayer' => '',
    'byName'   => '',
    'id'       => ''
));

$session = $HypixelAPI->getSession(array(
    'player' => ''
));

$friends = $HypixelAPI->getFriends(array(
    'player' => ''
));
```