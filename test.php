<?php

ini_set('display_errors', 'On');
ini_set('html_errors', 0);
error_reporting(-1);

include_once('HypixelPHP.php');
$HypixelPHP = new HypixelPHP(array('api_key'=>'63ba5ba5-d858-4399-9ff2-82d0322bf6a2'));
$player = $HypixelPHP->getPlayer(array('name'=>'Plancke'));
print($player->get('displayname'));
$guild = $player->getPlayerGuild($HypixelPHP);
print_r($guild->get('members.0.rank'));
