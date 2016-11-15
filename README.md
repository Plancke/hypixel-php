# Hypixel PHP

This is a PHP wrapper for the [Hypixel Public API](https://api.hypixel.net)
You can find Documentation and a Java implementation here: https://github.com/HypixelDev/PublicAPI

## Requirements
- PHP 5.6+
- Hypixel API key

## Usage

To interact with the API you need an API key, you can get a key by doing "/api" on the Hypixel Network.

```PHP
$HypixelPHP = new HypixelPHP('API_KEY');

// you can override modules, not required
$HypixelPHP->setCacheHandler(...);
$HypixelPHP->setLogger(...);
$HypixelPHP->setFetcher(...);
```

## Game Info
The [Game Info](https://github.com/Plancke/hypixel-php/tree/master/game_info) directory contains JSON files that have specific game items from the games that can be used in various locations.