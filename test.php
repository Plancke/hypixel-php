<?php

ini_set('display_errors', 'On');
ini_set('html_errors', 0);
error_reporting(-1);

include_once('HypixelPHP.php');
$HypixelPHP = new HypixelPHP(array('api_key'=>'0a724ac9-fa5e-433f-89f5-c0c28605bc5c'));
$player = $HypixelPHP->getPlayer(array('name'=>'Plancke'));