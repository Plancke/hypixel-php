# Hypixel PHP

A PHP class for fetching Player/Guild information from the Public HypixelAPI

## Usage

To interact with the API you need an API key, you can get a key by doing "/api" on the Hypixel Network.

    $HypixelAPI = new HypixelAPI(array(
         'api_key'              => '',
         'cache_time'           => '600',
         'cache_folder_player'  => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player/',
         'cache_uuid_table'     => 'uuid_table.json',
         'cache_folder_guild'   => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild/',
         'cache_byPlayer_table' => 'byPlayer_table.json',
         'cache_byName_table'   => 'byName_table.json'
     ));
     
All of these options are optional. They are provided above withtheir default values.

Once you have the API Object you can call the getter functions

    $player = $HypixelAPI->getPlayer(array(
        'name' => '',
        'uuid' => ''
    ));
    
    $player = $HypixelAPI->getGuild(array(
        'byPlayer' => '',
        'byName'   => '',
        'id'       => ''
    ));

## Caching
Caching is employed because we want to remove as much strain as possible from the Hypixel DB, thus providing us with more pleasure on the server. The Public API will throttle if there is too much traffic and this intends to avoid that from happening.