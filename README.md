# Hypixel PHP

A PHP class for fetching Player/Guild information from the Public HypixelAPI
Since version 1.2 data is saved in a different way. This'll require you to
cache everything again. This is fully automatic. 
Guild cache handling should be faster now.

## Usage

To interact with the API you need an API key, you can get a key by doing "/api" on the Hypixel Network.

```PHP
$HypixelAPI = new HypixelAPI(array(
    'api_key'               => '',
    'cache_time'            => '600',
    'cache_folder_player'   => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player/',
    'cache_uuid_table'      => 'uuid_table.json',
    'cache_folder_guild'    => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild/',
    'cache_byPlayer_table'  => 'byPlayer_table.json',
    'cache_byName_table'    => 'byName_table.json',
    'cache_folder_friends'  => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/friends/',
    'cache_folder_sessions' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/sessions/',
    'version'               => '1.2'
 ));
 ```
     
All of these options are optional. They are provided above withtheir default values.

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

## Caching
Caching is employed because we want to remove as much strain as possible from the Hypixel DB, thus providing us with more pleasure on the server. The Public API will throttle if there is too much traffic and this intends to avoid that from happening.
