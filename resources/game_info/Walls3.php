<?php
return array(
    'classes' =>
        array(
            'list' =>
                array(
                    'HUNTER' =>
                        array(
                            'id' => 'HUNTER',
                            'display' => 'Hunter',
                            'description' => 'Use superior archery skills to pelt enemies with arrows.',
                            'type' => 'HERO',
                            'categories' =>
                                array(
                                    0 => 'RANGED',
                                    1 => 'FIGHTER',
                                ),
                            'difficulty' => 3.0,
                        ),
                    'ZOMBIE' =>
                        array(
                            'id' => 'ZOMBIE',
                            'display' => 'Zombie',
                            'description' => 'The Zombie class focuses on defensive gameplay and healing.',
                            'type' => 'NORMAL',
                            'categories' =>
                                array(
                                    0 => 'TANK',
                                    1 => 'SUPPORT',
                                ),
                            'difficulty' => 2.0,
                            'starter' => true,
                        ),
                    'CREEPER' =>
                        array(
                            'id' => 'CREEPER',
                            'display' => 'Creeper',
                            'description' => 'The Creeper class uses explosion based powers to win.',
                            'type' => 'NORMAL',
                            'categories' =>
                                array(
                                    0 => 'DAMAGE',
                                    1 => 'CONTROL',
                                ),
                            'difficulty' => 4.0,
                        ),
                    'ARCANIST' =>
                        array(
                            'id' => 'ARCANIST',
                            'display' => 'Arcanist',
                            'description' => 'PLACEHOLDER',
                            'type' => 'HERO',
                            'categories' =>
                                array(
                                    0 => 'FIGHTER',
                                    1 => 'DAMAGE',
                                ),
                            'difficulty' => 1.0,
                        ),
                    'SHAMAN' =>
                        array(
                            'id' => 'SHAMAN',
                            'display' => 'Shaman',
                            'description' => 'PLACEHOLDER',
                            'type' => 'HERO',
                            'categories' =>
                                array(
                                    0 => 'FIGHTER',
                                    1 => 'CONTROL',
                                ),
                            'difficulty' => 3.0,
                        ),
                    'DREADLORD' =>
                        array(
                            'id' => 'DREADLORD',
                            'display' => 'Dreadlord',
                            'description' => 'PLACEHOLDER',
                            'type' => 'HERO',
                            'categories' =>
                                array(
                                    0 => 'RUSHER',
                                    1 => 'FIGHTER',
                                ),
                            'difficulty' => 1.0,
                        ),
                    'GOLEM' =>
                        array(
                            'id' => 'GOLEM',
                            'display' => 'Golem',
                            'description' => 'PLACEHOLDER',
                            'type' => 'HERO',
                            'categories' =>
                                array(
                                    0 => 'TANK',
                                    1 => 'FIGHTER',
                                ),
                            'difficulty' => 2.0,
                        ),
                    'SQUID' =>
                        array(
                            'id' => 'SQUID',
                            'display' => 'Squid',
                            'description' => 'Splashing around, like a Squid, because thats what Squids do. Splash.',
                            'type' => 'NORMAL',
                            'categories' =>
                                array(
                                    0 => 'TANK',
                                    1 => 'CONTROL',
                                ),
                            'difficulty' => 1.0,
                        ),
                    'MOLEMAN' =>
                        array(
                            'id' => 'MOLEMAN',
                            'display' => 'Moleman',
                            'description' => 'Wanna play wacka mole...?',
                            'type' => 'MYTHIC',
                            'categories' =>
                                array(
                                    0 => 'MOBILITY',
                                    1 => 'CONTROL',
                                ),
                            'difficulty' => 2.0,
                        ),
                    'ENDERMAN' =>
                        array(
                            'id' => 'ENDERMAN',
                            'display' => 'Enderman',
                            'description' => 'The Enderman class has special teleportation powers and endurance.',
                            'type' => 'NORMAL',
                            'categories' =>
                                array(
                                    0 => 'MOBILITY',
                                    1 => 'RUSHER',
                                ),
                            'difficulty' => 2.0,
                            'starter' => true,
                        ),
                    'HEROBRINE' =>
                        array(
                            'id' => 'HEROBRINE',
                            'display' => 'Herobrine',
                            'description' => 'The Herobrine class uses supernatural abilities to attack and destroy your enemies.',
                            'type' => 'NORMAL',
                            'categories' =>
                                array(
                                    0 => 'FIGHTER',
                                    1 => 'DAMAGE',
                                ),
                            'difficulty' => 1.0,
                            'starter' => true,
                        ),
                    'BLAZE' =>
                        array(
                            'id' => 'BLAZE',
                            'display' => 'Blaze',
                            'description' => 'The Blaze class uses the spirit of fire to enfore flames.',
                            'type' => 'HERO',
                            'categories' =>
                                array(
                                    0 => 'RANGED',
                                    1 => 'CONTROL',
                                ),
                            'difficulty' => 2.0,
                        ),
                    'PIGMAN' =>
                        array(
                            'id' => 'PIGMAN',
                            'display' => 'Pigman',
                            'description' => 'Half man, half pig, half..oh wait! Feel the power of pork!',
                            'type' => 'HERO',
                            'categories' =>
                                array(
                                    0 => 'FIGHTER',
                                    1 => 'SUPPORT',
                                ),
                            'difficulty' => 2.0,
                        ),
                    'SPIDER' =>
                        array(
                            'id' => 'SPIDER',
                            'display' => 'Spider',
                            'description' => 'The Spider class uses agile paths for combat.',
                            'type' => 'NORMAL',
                            'categories' =>
                                array(
                                    0 => 'MOBILITY',
                                    1 => 'RUSHER',
                                ),
                            'difficulty' => 3.0,
                        ),
                    'WEREWOLF' =>
                        array(
                            'id' => 'WEREWOLF',
                            'display' => 'Werewolf',
                            'description' => 'It\'s always full moon for this brawler.. Slice and dice your way to victory!',
                            'type' => 'MYTHIC',
                            'categories' =>
                                array(
                                    0 => 'TANK',
                                    1 => 'FIGHTER',
                                ),
                            'difficulty' => 2.0,
                        ),
                    'PIRATE' =>
                        array(
                            'id' => 'PIRATE',
                            'display' => 'Pirate',
                            'description' => 'Use cunning and wit to survive, or blow enemies up with parrots.',
                            'type' => 'HERO',
                            'categories' =>
                                array(
                                    0 => 'FIGHTER',
                                    1 => 'DAMAGE',
                                ),
                            'difficulty' => 3.0,
                        ),
                    'PHOENIX' =>
                        array(
                            'id' => 'PHOENIX',
                            'display' => 'Phoenix',
                            'description' => 'Born from the ashes of my predecessor I shall erase you from existence!',
                            'type' => 'MYTHIC',
                            'categories' =>
                                array(
                                    0 => 'RANGED',
                                    1 => 'SUPPORT',
                                ),
                            'difficulty' => 4.0,
                        ),
                    'SKELETON' =>
                        array(
                            'id' => 'SKELETON',
                            'display' => 'Skeleton',
                            'description' => 'The Skeleton class makes excellent use of ranged abilities and weapons.',
                            'type' => 'NORMAL',
                            'categories' =>
                                array(
                                    0 => 'RANGED',
                                    1 => 'MOBILITY',
                                ),
                            'difficulty' => 3.0,
                            'starter' => true,
                        ),
                    'ASSASSIN' =>
                        array(
                            'id' => 'ASSASSIN',
                            'display' => 'Assassin',
                            'description' => '',
                            'type' => 'MYTHIC',
                            'categories' =>
                                array(
                                    0 => 'DAMAGE',
                                    1 => 'RUSHER',
                                ),
                            'difficulty' => 2.0,
                        ),
                    'RENEGADE' =>
                        array(
                            'id' => 'RENEGADE',
                            'display' => 'Renegade',
                            'description' => '',
                            'type' => 'MYTHIC',
                            'categories' =>
                                array(
                                    0 => 'MOBILITY',
                                    1 => 'RANGED',
                                ),
                            'difficulty' => 3.0,
                        ),
                    'SNOWMAN' =>
                        array(
                            'id' => 'SNOWMAN',
                            'display' => 'Snowman',
                            'description' => '',
                            'type' => 'MYTHIC',
                            'categories' =>
                                array(
                                    0 => 'CONTROL',
                                    1 => 'FIGHTER',
                                ),
                            'difficulty' => 3.0,
                        ),
                    'AUTOMATON' =>
                        array(
                            'id' => 'AUTOMATON',
                            'display' => 'Automaton',
                            'description' => '',
                            'type' => 'MYTHIC',
                            'categories' =>
                                array(
                                    0 => 'CONTROL',
                                    1 => 'TANK',
                                ),
                            'difficulty' => 4.0,
                        ),
                    'COW' =>
                        array(
                            'id' => 'COW',
                            'display' => 'Cow',
                            'description' => '',
                            'type' => 'NORMAL',
                            'categories' =>
                                array(
                                    0 => 'TANK',
                                    1 => 'SUPPORT',
                                ),
                            'difficulty' => 2.0,
                        ),
                    'SHARK' =>
                        array(
                            'id' => 'SHARK',
                            'display' => 'Shark',
                            'description' => '',
                            'type' => 'NORMAL',
                            'categories' =>
                                array(
                                    0 => 'DAMAGE',
                                    1 => 'CONTROL',
                                ),
                            'difficulty' => 1.0,
                        ),
                    'SHEEP' =>
                        array(
                            'id' => 'SHEEP',
                            'display' => 'Sheep',
                            'description' => '',
                            'type' => 'NORMAL',
                            'categories' =>
                                array(
                                    0 => 'CONTROL',
                                    1 => 'SUPPORT',
                                ),
                            'difficulty' => 3.0,
                        ),
                    'ANGEL' =>
                        array(
                            'id' => 'ANGEL',
                            'display' => 'Angel',
                            'description' => '',
                            'type' => 'MYTHIC',
                            'categories' =>
                                array(
                                    0 => 'FIGHTER',
                                    1 => 'SUPPORT',
                                ),
                            'difficulty' => 3.0,
                        ),
                    'DRAGON' =>
                        array(
                            'id' => 'DRAGON',
                            'display' => 'Dragon',
                            'description' => '',
                            'type' => 'MYTHIC',
                            'categories' =>
                                array(
                                    0 => 'DAMAGE',
                                    1 => 'RANGED',
                                ),
                            'difficulty' => 4.0,
                        ),
                ),
            'skills' =>
                array(
                    'KIT' =>
                        array(
                            'id' => 'KIT',
                            'field' => 'classes.%class%.skill_level_d',
                            'cleanName' => 'Kit',
                            'maxLevel' => 5,
                        ),
                    'SKILL' =>
                        array(
                            'id' => 'SKILL',
                            'field' => 'classes.%class%.skill_level_a',
                            'cleanName' => 'Skill',
                            'maxLevel' => 5,
                        ),
                    'PASSIVE_1' =>
                        array(
                            'id' => 'PASSIVE_1',
                            'field' => 'classes.%class%.skill_level_b',
                            'cleanName' => 'Passive 1',
                            'maxLevel' => 3,
                        ),
                    'PASSIVE_2' =>
                        array(
                            'id' => 'PASSIVE_2',
                            'field' => 'classes.%class%.skill_level_c',
                            'cleanName' => 'Passive 2',
                            'maxLevel' => 3,
                        ),
                    'GATHERING' =>
                        array(
                            'id' => 'GATHERING',
                            'field' => 'classes.%class%.skill_level_g',
                            'cleanName' => 'Gathering',
                            'maxLevel' => 3,
                        ),
                ),
            'fields' =>
                array(
                    'PRESTIGE' =>
                        array(
                            'id' => 'PRESTIGE',
                            'field' => 'classes.%class%.prestige',
                            'cleanName' => 'Prestige',
                        ),
                    'ENDERCHEST' =>
                        array(
                            'id' => 'ENDERCHEST',
                            'field' => 'classes.%class%.enderchest_rows',
                            'cleanName' => 'Ender Chest',
                        ),
                ),
        ),
    'modes' =>
        array(
            'list' =>
                array(
                    'STANDARD' =>
                        array(
                            'display' => 'Normal',
                            'partyMax' => 4,
                            'playersMax' => 100,
                            'teamMax' => 25,
                        ),
                    'FACE_OFF' =>
                        array(
                            'display' => 'Faceoff Mode',
                            'partyMax' => 20,
                            'playersMax' => 40,
                            'teamMax' => 20,
                        ),
                    'PRACTICE' =>
                        array(
                            'display' => 'Practice Mode',
                            'partyMax' => 2,
                            'playersMax' => 100,
                            'teamMax' => 25,
                        ),
                    'GVG' =>
                        array(
                            'display' => 'Casual Brawl',
                            'partyMax' => 0,
                            'playersMax' => 24,
                            'teamMax' => 10,
                        ),
                ),
        ),
);