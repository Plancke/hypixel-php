<?php

ini_set('display_errors', 'On');
ini_set('html_errors', 0);
error_reporting(-1);

include_once('HypixelPHP.php');
$HypixelPHP = new HypixelPHP(array('api_key'=>'67082701-0ff4-4d88-b32c-167323f9c908'));
$player = $HypixelPHP->get_player(array('name'=>'Plancke'));
print_r($player->getRaw());
