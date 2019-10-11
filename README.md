# Hypixel PHP

This is a PHP wrapper for the [Hypixel Public API](https://api.hypixel.net)
You can find Documentation and a Java implementation here: https://github.com/HypixelDev/PublicAPI

Note that this version is VERY outdated and a lot of things will not work as intended.

## Requirements
- PHP 7+
- Hypixel API key

## Installation

The preferred method of installing this library is with
[Composer](https://getcomposer.org) by running the following from your project
root:

    $ composer require "plancke/hypixel-php=^1.2.0"
    
I don't push a new version for every minor resource update. You can use `$ composer require "plancke/hypixel-php=dev-master"` to always download latest.

## TODO

- Add examples

## Usage

To interact with the API you need an API key, you can get a key by doing "/api" on the Hypixel Network.

```PHP
$HypixelPHP = new HypixelPHP('API_KEY');

// you can override modules
$HypixelPHP->setCacheHandler(...);
$HypixelPHP->setLogger(...);
$HypixelPHP->setFetcher(...);

$player = $HypixelPHP->getPlayer([FetchParams::PLAYER_BY_NAME => 'Plancke']);
if ($player instanceof Player) {
    echo $player->getName();
}
```