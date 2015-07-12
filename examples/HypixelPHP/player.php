<?php
include_once('HypixelPHP.php');
$HypixelPHP = new HypixelPHP\HypixelPHP(['api_key' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx']);
// get a player object using the hypixel api object
$player = $HypixelPHP->getPlayer(['name' => 'Plancke']);
if ($player != null) {
    echo 'Name: ' . $player->getName();
    echo '<br>';
    echo 'Formatted Name: ' . $player->getFormattedName(true, true);
    echo '<br>';
    echo 'Paintball Kills: ' . $player->getStats()->getGame('Paintball')->get('kills', 0);
    echo '<br>';
    echo 'Rank: ' . $player->getRank();
    echo '<br>';
    echo 'Pre EULA Rank: ' . $player->getRank(true, true);
}