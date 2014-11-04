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

    foreach($members as $member) {
        $player = $HypixelPHP->getPlayer(array('name'=>$member['name']));
        $player_name = $player->getName();

        echo "<li>$player_name</li>";
    }

    echo '</ul>';
}