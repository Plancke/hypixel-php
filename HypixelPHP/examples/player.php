<?php
include_once('HypixelPHP.php');
$HypixelPHP = new HypixelPHP\HypixelPHP(['api_key' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx']);
// get a player object using the hypixel api object
$player = $HypixelPHP->getPlayer([\HypixelPHP\KEYS::PLAYER_BY_NAME => 'Plancke']);
if ($player != null) {
    echo 'Name: ' . $player->getName();
    echo '<br>';
    echo 'Formatted Name: ' . $player->getFormattedName(true, true);
    echo '<br>';
    echo 'Paintball Kills: ' . $player->getStats()->getGameFromID(\HypixelPHP\GameTypes::PAINTBALL)->getInt('kills');
    echo '<br>';
    echo 'Rank: ' . $player->getRank()->getCleanName();
    echo '<br>';
    echo 'Pre EULA Rank: ' . $player->getRank(true, true)->getCleanName();
} else {
    echo 'Player == null';
    print_r($HypixelPHP->getUrlErrors());
}