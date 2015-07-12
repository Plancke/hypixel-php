<?php
include_once('HypixelPHP.php');
$HypixelPHP = new HypixelPHP\HypixelPHP(['api_key' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx']);
$player = $HypixelPHP->getPlayer(['name' => 'Plancke']);
if ($player != null) {
    echo $player->getName();

    // automatically saves the file again
    // combine multiple changes into the input
    // array rather than doing seperate
    // statements, less file saving!

    // use
    $player->setExtra([
        'hidden' => true,
        'cool' => true
    ]);
    // instead of
    $player->setExtra(['hidden' => true]);
    $player->setExtra(['cool' => true]);

    // Example use
    $extra = $player->getExtra();
    if (array_key_exists('hidden', $extra)) {
        if ($extra['hidden']) {
            echo 'Player hidden';
            exit;
        }
    }
}

