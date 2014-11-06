<?php
include_once('HypixelPHP.php');
// instantiate the hypixel api object using your api key
$HypixelPHP = new HypixelPHP(array('api_key'=>'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'));
// get a player object using the hypixel api object
$player = $HypixelPHP->getPlayer(array('name'=>'Plancke'));
// once you have the player object you can do whatever you want :D
echo $player->getName();

$allstats = $player->getStats();
foreach(array_keys($allstats->getRaw()) as $game)
{
    echo '<h1>' . $game . '</h1>';
    echo '<ul>';

    $gamestats = $allstats->getGame($game);
    foreach($allstats->getGame($game)->getRaw() as $field=>$val)
    {
        echo '<li><b>' . $field . ':</b> ' . $val . '</li>';
    }
    echo '</ul><br />';
}

echo $player->getStats()->getGame('Paintball')->get('kills', 0);
echo $player->getStats()->getGame('Paintball')->get('deaths', 0);