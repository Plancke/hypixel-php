<?php
include_once('HypixelPHP.php');
$HypixelPHP = new HypixelPHP\HypixelPHP(['api_key' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx']);
$guild = $HypixelPHP->getGuild([\HypixelPHP_Mongo\KEYS::GUILD_BY_PLAYER_NAME => 'Plancke']);
if ($guild != null) {
    echo 'Guild Name: ' . $guild->getName();
    if ($guild->canTag()) {
        echo 'Guild Tag: ' . $guild->getTag();
    }

    // Don't want to load players here, big guilds would
    // throttle your key. We're only getting the exact name
    // from cache or new if player doesn't exist yet
    $HypixelPHP->setCacheTime(24 * 60 * 60, \HypixelPHP\CACHE_TIMES::PLAYER);

    echo '<ul>';

    $memberList = $guild->getMemberList();
    foreach ($memberList->getList() as $rank => $members) {
        echo "<h1>$rank</h1>";
        foreach ($members as $member) {
            /** @var $member \HypixelPHP\GuildMember */
            $player = $member->getPlayer();
            if ($player == null) continue;

            echo '<li>' . $player->getName() . '</li>';
        }
    }

    echo '</ul>';
} else {
    echo 'Guild == null';
    print_r($HypixelPHP->getUrlErrors());
}