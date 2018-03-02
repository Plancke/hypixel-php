<?php
return array(
    'kits' =>
        array(
            '__desc' => 'All available kits, if cost is not present the kit is unlocked by default. Else the player will have {package} in their packages.Total kit level is determined by upgrades stored as {{package}_{upgradeType}}',
            'list' =>
                array(
                    'ARCHER' =>
                        array(
                            'display' => 'Archer',
                            'package' => 'kit_archer',
                            'description' => 'Archers specialize in long-range combat by using a powerful bow and enhanced arrows.',
                        ),
                    'SCOUT' =>
                        array(
                            'display' => 'Scout',
                            'package' => 'kit_scout',
                            'description' => 'Scouts are fast and agile. They are able to get to chests and other areas faster than other kits.',
                            'cost' => 1000,
                        ),
                    'GUARDIAN' =>
                        array(
                            'display' => 'Guardian',
                            'package' => 'kit_guardian',
                            'description' => 'Guardians become resistant when their health gets low and have access to defensive splash potions.',
                        ),
                    'SWORDSMAN' =>
                        array(
                            'display' => 'Swordsman',
                            'package' => 'kit_swordsman',
                            'description' => 'Swordsmen have mastered a sword technique that allows them to deal extra damage when they first hit a target.',
                        ),
                    'CLERIC' =>
                        array(
                            'display' => 'Cleric',
                            'package' => 'kit_cleric',
                            'description' => 'Cleric use magical healing to keep them and their teammates topped up.',
                            'cost' => 1000,
                        ),
                    'FROST_KNIGHT' =>
                        array(
                            'display' => 'Frost Knight',
                            'package' => 'kit_frost_knight',
                            'description' => 'Frost Knights use snowballs and snow golems to knock enemies back and gain speed boosts.',
                            'cost' => 1000,
                        ),
                    'ASSASSIN' =>
                        array(
                            'display' => 'Assassin',
                            'package' => 'kit_assassin',
                            'description' => 'Assassins use ender pearls to teleport around and turn invisible, allowing them to take opponents by surprise.',
                            'cost' => 1000,
                        ),
                    'NECROMANCER' =>
                        array(
                            'display' => 'Necromancer',
                            'package' => 'kit_necromancer',
                            'description' => 'Necromancers control undead minions to do their bidding and can turn dead opponents into zombies.',
                            'cost' => 1000,
                        ),
                    'BERSERKER' =>
                        array(
                            'display' => 'Berserker',
                            'package' => 'kit_berserker',
                            'description' => 'Berserkers are offensive powerhouses, becoming even stronger when their health gets low.',
                            'cost' => 1000,
                        ),
                    'JUMPMAN' =>
                        array(
                            'display' => 'Jumpman',
                            'package' => 'kit_jumpman',
                            'description' => 'Jumpman was raised by frogs, they taught him how to jump away from his problems.',
                            'cost' => 1000,
                        ),
                    'TREASURE_HUNTER' =>
                        array(
                            'display' => 'Treasure Hunter',
                            'package' => 'kit_treasure_hunter',
                            'description' => 'Treasure Hunters really like looting chests, which invigorate them with random buffs and sometimes give them super powerful gear!',
                            'cost' => 1000,
                        ),
                ),
            'upgradeCosts' =>
                array(
                    'MINOR' =>
                        array(
                            0 => 2000,
                            1 => 4000,
                            2 => 10000,
                            3 => 20000,
                            4 => 50000,
                            5 => 100000,
                            6 => 250000,
                        ),
                    'MAJOR' => -1,
                    'MASTER' => 500000,
                ),
        ),
    'cards' =>
        array(
            '__desc' => 'Card levels are stored as {package}, duplicates stored as {{package}_duplicates}.',
            'list' =>
                array(
                    'ALCHEMY' =>
                        array(
                            'display' => 'Alchemy',
                            'package' => 'perk_alchemy',
                            'tier' => 'RARE',
                        ),
                    'ARROW_DEFLECTION' =>
                        array(
                            'display' => 'Arrow Deflection',
                            'package' => 'perk_arrow_deflection',
                            'tier' => 'LEGENDARY',
                        ),
                    'BLAST_PROTECTION' =>
                        array(
                            'display' => 'Blast Protection',
                            'package' => 'perk_blast_protection',
                            'tier' => 'COMMON',
                        ),
                    'BLAZING_ARROWS' =>
                        array(
                            'display' => 'Blazing Arrows',
                            'package' => 'perk_blazing_arrows',
                            'tier' => 'RARE',
                        ),
                    'CHICKEN_BOW' =>
                        array(
                            'display' => 'Chicken Bow',
                            'package' => 'perk_chicken_bow',
                            'tier' => 'LEGENDARY',
                        ),
                    'CREEPER' =>
                        array(
                            'display' => 'Creeper',
                            'package' => 'perk_creeper',
                            'tier' => 'COMMON',
                        ),
                    'DAMAGE_POTION' =>
                        array(
                            'display' => 'Damage Potion',
                            'package' => 'perk_damage_potion',
                            'tier' => 'COMMON',
                        ),
                    'ENDERMAN' =>
                        array(
                            'display' => 'Enderman',
                            'package' => 'perk_enderman',
                            'tier' => 'LEGENDARY',
                        ),
                    'ENDLESS_QUIVER' =>
                        array(
                            'display' => 'Endless Quiver',
                            'package' => 'perk_endless_quiver',
                            'tier' => 'COMMON',
                        ),
                    'ENERGY_DRINK' =>
                        array(
                            'display' => 'Energy Drink',
                            'package' => 'perk_energy_drink',
                            'tier' => 'RARE',
                        ),
                    'EXPLOSIVE_BOW' =>
                        array(
                            'display' => 'Explosive Bow',
                            'package' => 'perk_explosive_bow',
                            'tier' => 'LEGENDARY',
                        ),
                    'FRUIT_FINDER' =>
                        array(
                            'display' => 'Fruit Finder',
                            'package' => 'perk_fruit_finder',
                            'tier' => 'COMMON',
                        ),
                    'GUARDIAN' =>
                        array(
                            'display' => 'Guardian',
                            'package' => 'perk_guardian',
                            'tier' => 'RARE',
                        ),
                    'HEADSTART' =>
                        array(
                            'display' => 'Headstart',
                            'package' => 'perk_headstart',
                            'tier' => 'LEGENDARY',
                        ),
                    'HEARTY_START' =>
                        array(
                            'display' => 'Hearty Start',
                            'package' => 'perk_hearty_start',
                            'tier' => 'RARE',
                        ),
                    'HIT_AND_RUN' =>
                        array(
                            'display' => 'Hit And Run',
                            'package' => 'perk_hit_and_run',
                            'tier' => 'COMMON',
                        ),
                    'HONED_BOW' =>
                        array(
                            'display' => 'Honed Bow',
                            'package' => 'perk_honed_bow',
                            'tier' => 'RARE',
                        ),
                    'INVISIBILITY' =>
                        array(
                            'display' => 'Invisibility',
                            'package' => 'perk_invisibility',
                            'tier' => 'RARE',
                        ),
                    'IRON_GOLEM' =>
                        array(
                            'display' => 'Iron Golem',
                            'package' => 'perk_iron_golem',
                            'tier' => 'LEGENDARY',
                        ),
                    'MARKSMAN' =>
                        array(
                            'display' => 'Marksman',
                            'package' => 'perk_marksman',
                            'tier' => 'COMMON',
                        ),
                    'NUTRITIOUS' =>
                        array(
                            'display' => 'Nutritious',
                            'package' => 'perk_nutritious',
                            'tier' => 'LEGENDARY',
                        ),
                    'PACIFY' =>
                        array(
                            'display' => 'Pacify',
                            'package' => 'perk_pacify',
                            'tier' => 'COMMON',
                        ),
                    'PEARL_ABSORPTION' =>
                        array(
                            'display' => 'Pearl Absorption',
                            'package' => 'perk_pearl_absorption',
                            'tier' => 'COMMON',
                        ),
                    'RAMPAGE' =>
                        array(
                            'display' => 'Rampage',
                            'package' => 'perk_rampage',
                            'tier' => 'LEGENDARY',
                        ),
                    'REGENERATION' =>
                        array(
                            'display' => 'Regeneration',
                            'package' => 'perk_regeneration',
                            'tier' => 'RARE',
                        ),
                    'RESISTANT' =>
                        array(
                            'display' => 'Resistant',
                            'package' => 'perk_resistant',
                            'tier' => 'COMMON',
                        ),
                    'SHARPENED_SWORD' =>
                        array(
                            'display' => 'Sharpened Sword',
                            'package' => 'perk_sharpened_sword',
                            'tier' => 'RARE',
                        ),
                    'SKELETON_JOCKEY' =>
                        array(
                            'display' => 'Skeleton Jockey',
                            'package' => 'perk_skeleton_jockey',
                            'tier' => 'RARE',
                        ),
                    'SNOW_GOLEM' =>
                        array(
                            'display' => 'Snow Golem',
                            'package' => 'perk_snow_golem',
                            'tier' => 'RARE',
                        ),
                    'SUGAR_RUSH' =>
                        array(
                            'display' => 'Sugar Rush',
                            'package' => 'perk_sugar_rush',
                            'tier' => 'COMMON',
                        ),
                    'SUPPLY_DROP' =>
                        array(
                            'display' => 'Supply Drop',
                            'package' => 'perk_supply_drop',
                            'tier' => 'RARE',
                        ),
                    'TRIPLESHOT' =>
                        array(
                            'display' => 'Triple Shot',
                            'package' => 'perk_tripleshot',
                            'tier' => 'LEGENDARY',
                        ),
                    'WINGED_BOOTS' =>
                        array(
                            'display' => 'Winged Boots',
                            'package' => 'perk_winged_boots',
                            'tier' => 'LEGENDARY',
                        ),
                    'WITCH' =>
                        array(
                            'display' => 'Witch',
                            'package' => 'perk_witch',
                            'tier' => 'LEGENDARY',
                        ),
                    'VOID_MAGNET' =>
                        array(
                            'display' => 'Void Magnet',
                            'package' => 'perk_void_magnet',
                            'tier' => 'COMMON',
                        ),
                    'VOID_WARRANTY' =>
                        array(
                            'display' => 'Void Warranty',
                            'package' => 'perk_void_warranty',
                            'tier' => 'LEGENDARY',
                        ),
                    'ELVEN_ARCHER' =>
                        array(
                            'display' => 'Elven Archer',
                            'package' => 'perk_elven_archer',
                            'tier' => 'COMMON',
                        ),
                    'BIGGER_BANGS' =>
                        array(
                            'display' => 'Bigger Bangs',
                            'package' => 'perk_bigger_bangs',
                            'tier' => 'COMMON',
                        ),
                    'MUSHROOM_AURA' =>
                        array(
                            'display' => 'Mushroom Aura',
                            'package' => 'perk_mushroom_aura',
                            'tier' => 'RARE',
                        ),
                    'BAT_SHIELD' =>
                        array(
                            'display' => 'Bat Shield',
                            'package' => 'perk_bat_shield',
                            'tier' => 'RARE',
                        ),
                    'FLOWER_POWER' =>
                        array(
                            'display' => 'Flower Power',
                            'package' => 'perk_flower_power',
                            'tier' => 'LEGENDARY',
                        ),
                    'NUCLEAR_SOLUTION' =>
                        array(
                            'display' => 'Nuclear Solution',
                            'package' => 'perk_nuclear_solution',
                            'tier' => 'LEGENDARY',
                        ),
                    'NO_CHEST_CHALLENGE' =>
                        array(
                            'display' => 'No Chest Challenge',
                            'package' => 'perk_no_chest_challenge',
                            'tier' => 'COMMON',
                        ),
                    'ARCHER_CHALLENGE' =>
                        array(
                            'display' => 'Archer Challenge',
                            'package' => 'perk_archer_challenge',
                            'tier' => 'RARE',
                        ),
                    'UHC_CHALLENGE' =>
                        array(
                            'display' => 'UHC Challenge',
                            'package' => 'perk_uhc_challenge',
                            'tier' => 'LEGENDARY',
                        ),
                    'LIFESTEAL' =>
                        array(
                            'display' => 'Lifesteal',
                            'package' => 'perk_lifesteal',
                            'tier' => 'COMMON',
                        ),
                    'VOID_CHEST' =>
                        array(
                            'display' => 'Void Chest',
                            'package' => 'perk_void_chest',
                            'tier' => 'RARE',
                        ),
                    'DOUBLE_JUMP' =>
                        array(
                            'display' => 'Double Jump',
                            'package' => 'perk_double_jump',
                            'tier' => 'RARE',
                        ),
                    'MONSTER_HUNTER' =>
                        array(
                            'display' => 'Monster Hunter',
                            'package' => 'perk_monster_hunter',
                            'tier' => 'COMMON',
                        ),
                    'HEAD_HUNTER' =>
                        array(
                            'display' => 'Head Hunter',
                            'package' => 'perk_head_hunter',
                            'tier' => 'LEGENDARY',
                        ),
                    'BOUNTY_HUNTER' =>
                        array(
                            'display' => 'Bounty Hunter',
                            'package' => 'perk_bounty_hunter',
                            'tier' => 'LEGENDARY',
                        ),
                    'HALF_HEALTH_CHALLENGE' =>
                        array(
                            'display' => 'Half Health Challenge',
                            'package' => 'perk_half_health_challenge',
                            'tier' => 'COMMON',
                        ),
                    'ULTIMATE_WARRIOR_CHALLENGE' =>
                        array(
                            'display' => 'Ultimate Warrior Challenge',
                            'package' => 'perk_ultimate_warrior_challenge',
                            'tier' => 'RARE',
                        ),
                    'BOMBER_CHALLENGE' =>
                        array(
                            'display' => 'Bomber Challenge',
                            'package' => 'perk_bomber_challenge',
                            'tier' => 'LEGENDARY',
                        ),
                ),
            'duplicatesNeeded' =>
                array(
                    0 => 0,
                    1 => 2,
                    2 => 8,
                    3 => 32,
                    4 => 100,
                ),
        ),
    'trackedStats' =>
        array(
            '__desc' => 'Stats that are tracked in this gamemode, stored globally as {field} and per kit as {{field}_{kitPackage}}',
            'list' =>
                array(
                    0 => 'kills',
                    1 => 'melee_kills',
                    2 => 'bow_kills',
                    3 => 'void_kills',
                    4 => 'mob_kills',
                    5 => 'longest_bow_shot',
                    6 => 'longest_bow_kill',
                    7 => 'bow_shots',
                    8 => 'bow_hits',
                    9 => 'mobs_killed',
                    10 => 'fastest_win_solo',
                    11 => 'fastest_win_doubles',
                    12 => 'fastest_win_team_war',
                    13 => 'fastest_win_mega',
                    14 => 'most_kills_game',
                    15 => 'enderchests_opened',
                    16 => 'solo_wins',
                    17 => 'doubles_wins',
                    18 => 'team_war_wins',
                    19 => 'mega_wins',
                    20 => 'games_played',
                    21 => 'assists',
                    22 => 'deaths',
                    23 => 'damage',
                    24 => 'mobs_spawned',
                    25 => 'cute_pants_found',
                    26 => 'quits',
                ),
        ),
    'modes' =>
        array(
            '__desc' => 'All modes this game supports',
            'list' =>
                array(
                    'SOLO' =>
                        array(
                            'name' => 'Solo',
                        ),
                    'DOUBLES' =>
                        array(
                            'name' => 'Doubles',
                        ),
                    'TEAM_WAR' =>
                        array(
                            'name' => 'Team War',
                        ),
                    'MEGA' =>
                        array(
                            'name' => 'Mega',
                        ),
                ),
        ),
);
