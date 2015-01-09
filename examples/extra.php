<?php
include_once('HypixelPHP.php');
$HypixelPHP = new HypixelPHP(array('api_key' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'));
$player = $HypixelPHP->getPlayer(array('name' => 'Plancke'));
if (!$player->isNull()) {
    echo $player->getName();

    // automatically saves the file again!
    // combine multiple changes into the input
    // array rather than doing seperate
    // statements, less file saving!

    $player->setExtra(array(
        'hidden' => true,
        'cool' => true
    ));
    //instead of
    $player->setExtra(array('hidden' => true));
    $player->setExtra(array('cool' => true));

    // Example use
    $extra = $player->getExtra();
    if (array_key_exists('hidden', $extra)) {
        if ($extra['hidden']) {
            echo 'Player hidden';
            exit;
        }
    }
}

