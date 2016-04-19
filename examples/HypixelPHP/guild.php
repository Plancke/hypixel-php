<?php
include_once('HypixelPHP.php');
$HypixelPHP = new HypixelPHP\HypixelPHP(['api_key' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx']);
$guild = $HypixelPHP->getGuild([\HypixelPHP_Mongo\KEYS::GUILD_BY_PLAYER_NAME => 'Plancke']);
if ($guild != null) {
    $memberList = $guild->getMemberList()->getList();

    echo 'Guild Name: ' . $guild->getName();
    if ($guild->canTag()) {
        echo 'Guild Tag: ' . $guild->getTag();
    }

    foreach ($memberList as $rank => $members) {
        echo "<h1>$rank</h1>";

        // Don't want to load players here, big guilds would
        // throttle your key. We're only getting the exact name
        // from cache or new if player doesn't exist yet
        echo '<ul>';
        $HypixelPHP->set(['cache_time' => $HypixelPHP::MAX_CACHE_TIME]);
        foreach ($members as $member) {
            if (isset($member['uuid'])) {
                $player = $HypixelPHP->getPlayer(['uuid' => $member['uuid']]);
            } else if (isset($member['name'])) {
                $player = $HypixelPHP->getPlayer(['name' => $member['name']]);
            } else {
                continue;
            }
            if ($player == null) {
                echo '<li>player==null</li>';
                continue;
            }

            echo '<li>' . $player->getName() . '</li>';
        }

        echo '</ul>';
    }
} else {
    echo 'Guild == null';
    print_r($HypixelPHP->getUrlErrors());
}