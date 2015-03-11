<?php
include_once('HypixelPHP.php');
$HypixelPHP = new HypixelPHP\HypixelPHP(array('api_key' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'));
$guild = $HypixelPHP->getGuild(array('byName' => 'PainBall'));
if ($guild != null) {
    $memberlist = $guild->getMemberList()->getList();

    echo 'Guild Name: ' . $guild->getName();
    if ($guild->canTag()) {
        echo 'Guild Tag: ' . $guild->getTag();
    }

    foreach ($memberlist as $rank => $members) {
        echo "<h1>$rank</h1>";

        // Don't want to load players here, big guilds would
        // throttle your key. We're only getting the exact name
        // from cache or new if player doesn't exist yet
        echo '<ul>';
        $HypixelPHP->set(array('cache_time' => $HypixelPHP::MAX_CACHE_TIME));
        foreach ($members as $member) {
            $player = $HypixelPHP->getPlayer(array('name' => $member['name']));
            $player_name = $player->getName();

            echo "<li>$player_name</li>";
        }

        echo '</ul>';
    }
}
