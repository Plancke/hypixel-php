<?php
include_once('HypixelPHP.php');
// instantiate the hypixel api object using your api key
$HypixelPHP = new HypixelPHP(array('api_key'=>'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'));
// get a player object using the hypixel api object
$player = $HypixelPHP->getPlayer(array('name'=>'Plancke'));
// once you have the player object you can do whatever you want :D
echo $player->getName();