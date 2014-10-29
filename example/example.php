<?php
$HypixelPHP = new HypixelPHP(array('api_key'=>'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'));
$player = $HypixelPHP->getPlayer(array('name'=>'Plancke'));
echo $player->getName();
$guild = $HypixelPHP->getGuild(array('byName'=>'PainBall'));
echo $guild->getName();
