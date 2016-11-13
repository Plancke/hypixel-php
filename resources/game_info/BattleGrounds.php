<?php
$_INFO_BATTLEGROUNDS = array(
    'classes' =>
        array(
            'mage' =>
                array(
                    'spec' =>
                        array(
                            'pyromancer' =>
                                array(
                                    'display' => 'Pyromancer',
                                    'cost' => 0,
                                ),
                            'cryomancer' =>
                                array(
                                    'display' => 'Cryomancer',
                                    'cost' => 5000,
                                ),
                            'aquamancer' =>
                                array(
                                    'display' => 'Aquamancer',
                                    'cost' => 15000,
                                ),
                        ),
                    'display' => 'Mage',
                ),
            'warrior' =>
                array(
                    'spec' =>
                        array(
                            'berserker' =>
                                array(
                                    'display' => 'Berserker',
                                    'cost' => 0,
                                ),
                            'defender' =>
                                array(
                                    'display' => 'Defender',
                                    'cost' => 15000,
                                ),
                        ),
                    'display' => 'Warrior',
                ),
            'paladin' =>
                array(
                    'spec' =>
                        array(
                            'avenger' =>
                                array(
                                    'display' => 'Avenger',
                                    'cost' => 0,
                                ),
                            'crusader' =>
                                array(
                                    'display' => 'Crusader',
                                    'cost' => 5000,
                                ),
                            'protector' =>
                                array(
                                    'display' => 'Protector',
                                    'cost' => 15000,
                                ),
                        ),
                    'display' => 'Paladin',
                ),
            'shaman' =>
                array(
                    'spec' =>
                        array(
                            'thunderlord' =>
                                array(
                                    'display' => 'Thunderlord',
                                    'cost' => 0,
                                ),
                            'earthwarden' =>
                                array(
                                    'display' => 'Earthwarden',
                                    'cost' => 0,
                                ),
                        ),
                ),
        ),
    'classUpgrades' =>
        array(
            'skill' =>
                array(
                    'fields' =>
                        array(
                            0 => 'skill1',
                            1 => 'skill2',
                            2 => 'skill3',
                            3 => 'skill4',
                            4 => 'skill5',
                        ),
                    'costs' =>
                        array(
                            0 => 1560,
                            1 => 2350,
                            2 => 3750,
                            3 => 6400,
                            4 => 11500,
                            5 => 21900,
                            6 => 43750,
                            7 => 91900,
                            8 => 202500,
                        ),
                ),
            'combat' =>
                array(
                    'fields' =>
                        array(
                            0 => 'energy',
                            1 => 'health',
                            2 => 'cooldown',
                            3 => 'critchance',
                            4 => 'critmultiplier',
                        ),
                    'costs' =>
                        array(
                            0 => 780,
                            1 => 1175,
                            2 => 1875,
                            3 => 3200,
                            4 => 5750,
                            5 => 10950,
                            6 => 21875,
                            7 => 45950,
                            8 => 101250,
                        ),
                ),
        ),
    'repairCost' => 10,
);