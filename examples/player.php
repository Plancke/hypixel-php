<?php
include_once('HypixelPHP.php');
// instantiate the hypixel api object using your api key
$HypixelPHP = new HypixelPHP(array('api_key' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'));
// get a player object using the hypixel api object
$player = $HypixelPHP->getPlayer(array('name' => 'Plancke'));
// Check if Object is null
// All Objects returned by this Wrapper have this function
if (!$player->isNull()) {
    echo $player->getName();

    $allstats = $player->getStats();
    $games = array_keys($allstats->getRaw());
    foreach ($games as $game) {
        echo '<h1>' . $game . '</h1>';
        echo '<ul>';
        $gamestats = $allstats->getGame($game);
        foreach ($allstats->getGame($game)->getRaw() as $field => $val) {
            echo "<li><b>$field:</b> $val</li>";
        }
        echo '</ul>';
    }

    echo $player->getStats()->getGame('Paintball')->get('kills', 0);
    echo $player->getStats()->getGame('Paintball')->get('deaths', 0);
}