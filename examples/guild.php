<?php
$HypixelPHP = new HypixelPHP(array('api_key'=>'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'));
$guild = $HypixelPHP->getGuild(array('byName'=>'PainBall'));
$memberlist = $guild->getMemberList()->getList();

echo 'Guild Name: ' . $guild->getName();
echo 'Guild Tag: ' . $guild->getTag();

foreach($memberlist as $rank=>$members)
{
    echo "<h1>$rank</h1>";
    echo '<ul>';

    // Don't want to load players here, big guilds
    // We're only getting the exact name from cache or
    // new if player doesn't exist yet
    $HypixelPHP->set(array('cache_time'=>'9999999999999'));
    foreach($members as $member) {
        $player = $HypixelPHP->getPlayer(array('name'=>$member['name']));
        $player_name = $player->getName();

        echo "<li>$player_name</li>";
    }

    echo '</ul>';
}
