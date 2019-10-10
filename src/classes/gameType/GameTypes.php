<?php

namespace Plancke\HypixelPHP\classes\gameType;

use Closure;

/**
 * Class GameTypes
 * @package Plancke\HypixelPHP\classes\gameType
 */
class GameTypes {
    const QUAKE = 2;
    const WALLS = 3;
    const PAINTBALL = 4;
    const SURVIVAL_GAMES = 5;
    const TNTGAMES = 6;
    const VAMPIREZ = 7;
    const WALLS3 = 13;
    const ARCADE = 14;
    const ARENA = 17;
    const UHC = 20;
    const MCGO = 21;
    const BATTLEGROUND = 23;
    const SUPER_SMASH = 24;
    const GINGERBREAD = 25;
    const HOUSING = 26;
    const SKYWARS = 51;
    const TRUE_COMBAT = 52;
    const SPEED_UHC = 54;
    const SKYCLASH = 55;
    const LEGACY = 56;
    const PROTOTYPE = 57;
    const BEDWARS = 58;
    const MURDER_MYSTERY = 59;
    const BUILD_BATTLE = 60;
    const DUELS = 61;
    const SKYBLOCK = 63;
    const PIT = 64;

    /**
     * @param $db
     * @return GameType|null
     */
    public static function fromDbName($db) {
        return self::fromX(function (GameType $gameType) use ($db) {
            return strtolower($gameType->getDb()) == strtolower($db);
        });
    }

    /**
     * @param Closure $test
     * @return GameType|null
     */
    public static function fromX(Closure $test) {
        foreach (GameTypes::values() as $id) {
            $gameType = GameTypes::fromID($id);
            if ($gameType != null) {
                if ($test($gameType)) {
                    return $gameType;
                }
            }
        }
        return null;
    }

    /**
     * @return int[]
     */
    public static function values() {
        return [
            self::QUAKE,
            self::WALLS,
            self::PAINTBALL,
            self::SURVIVAL_GAMES,
            self::TNTGAMES,
            self::VAMPIREZ,
            self::WALLS3,
            self::ARCADE,
            self::ARENA,
            self::UHC,
            self::MCGO,
            self::BATTLEGROUND,
            self::SUPER_SMASH,
            self::GINGERBREAD,
            self::HOUSING,
            self::SKYWARS,
            self::TRUE_COMBAT,
            self::SPEED_UHC,
            self::SKYCLASH,
            self::LEGACY,
            self::PROTOTYPE,
            self::BEDWARS,
            self::MURDER_MYSTERY,
            self::BUILD_BATTLE,
            self::DUELS,
            self::SKYBLOCK,
            self::PIT,
        ];
    }

    /**
     * @param $id
     *
     * @return GameType|null
     */
    public static function fromID($id) {
        switch ($id) {
            case GameTypes::QUAKE:
                return new GameType('QUAKE', 'Quake', 'Quake', 'Quake', GameTypes::QUAKE, false);
            case GameTypes::WALLS:
                return new GameType('WALLS', 'Walls', 'Walls', 'Walls', GameTypes::WALLS, false);
            case GameTypes::PAINTBALL:
                return new GameType('PAINTBALL', 'Paintball', 'Paintball', 'Paintball', GameTypes::PAINTBALL, false);
            case GameTypes::SURVIVAL_GAMES:
                return new GameType('SURVIVAL_GAMES', 'HungerGames', 'Blitz Survival Games', 'BSG', GameTypes::SURVIVAL_GAMES);
            case GameTypes::TNTGAMES:
                return new GameType('TNTGAMES', 'TNTGames', 'TNT Games', 'TNT Games', GameTypes::TNTGAMES);
            case GameTypes::VAMPIREZ:
                return new GameType('VAMPIREZ', 'VampireZ', 'VampireZ', 'VampireZ', GameTypes::VAMPIREZ, false);
            case GameTypes::WALLS3:
                return new GameType('WALLS3', 'Walls3', 'Mega Walls', 'MW', GameTypes::WALLS3);
            case GameTypes::ARCADE:
                return new GameType('ARCADE', 'Arcade', 'Arcade', 'Arcade', GameTypes::ARCADE);
            case GameTypes::ARENA:
                return new GameType('ARENA', 'Arena', 'Arena Brawl', 'Arena', GameTypes::ARENA, false);
            case GameTypes::UHC:
                return new GameType('UHC', 'UHC', 'UHC Champions', 'UHC', GameTypes::UHC);
            case GameTypes::MCGO:
                return new GameType('MCGO', 'MCGO', 'Cops and Crims', 'CaC', GameTypes::MCGO);
            case GameTypes::BATTLEGROUND:
                return new GameType('BATTLEGROUND', 'Battleground', 'Warlords', 'Warlords', GameTypes::BATTLEGROUND);
            case GameTypes::SUPER_SMASH:
                return new GameType('SUPER_SMASH', 'SuperSmash', 'Smash Heroes', 'Smash Heroes', GameTypes::SUPER_SMASH);
            case GameTypes::GINGERBREAD:
                return new GameType('GINGERBREAD', 'GingerBread', 'Turbo Kart Racers', 'TKR', GameTypes::GINGERBREAD, false);
            case GameTypes::HOUSING:
                return new GameType('HOUSING', 'Housing', 'Housing', 'Housing', GameTypes::HOUSING, false);
            case GameTypes::SKYWARS:
                return new GameType('SKYWARS', 'SkyWars', 'SkyWars', 'SkyWars', GameTypes::SKYWARS);
            case GameTypes::TRUE_COMBAT:
                return new GameType('TRUE_COMBAT', 'TrueCombat', 'Crazy Walls', 'Crazy Walls', GameTypes::TRUE_COMBAT);
            case GameTypes::SPEED_UHC:
                return new GameType('SPEED_UHC', 'SpeedUHC', 'Speed UHC', 'Speed UHC', GameTypes::SPEED_UHC);
            case GameTypes::SKYCLASH:
                return new GameType('SKYCLASH', 'SkyClash', 'SkyClash', 'SkyClash', GameTypes::SKYCLASH);
            case GameTypes::LEGACY:
                return new GameType('LEGACY', 'Legacy', 'Classic Games', 'Classic', GameTypes::LEGACY);
            case GameTypes::PROTOTYPE:
                return new GameType('PROTOTYPE', 'Prototype', 'Prototype', 'Prototype', GameTypes::PROTOTYPE, false);
            case GameTypes::BEDWARS:
                return new GameType('BEDWARS', 'Bedwars', 'Bed Wars', 'Bed Wars', GameTypes::BEDWARS, false);
            case GameTypes::MURDER_MYSTERY:
                return new GameType('MURDER_MYSTERY', 'MurderMystery', 'Murder Mystery', 'Murder Mystery', GameTypes::MURDER_MYSTERY, false);
            case GameTypes::BUILD_BATTLE:
                return new GameType('BUILD_BATTLE', 'BuildBattle', 'Build Battle', 'Build Battle', GameTypes::BUILD_BATTLE, false);
            case GameTypes::DUELS:
                return new GameType('DUELS', 'Duels', 'Duels', 'Duels', GameTypes::DUELS, false);
            case GameTypes::SKYBLOCK:
                return new GameType('SKYBLOCK', 'SkyBlock', 'SkyBlock', 'SkyBlock', GameTypes::SKYBLOCK, false);
            case GameTypes::PIT:
                return new GameType('PIT', 'Pit', 'Pit', 'Pit', GameTypes::PIT, false);
            default:
                return null;
        }
    }

    /**
     * @param $short
     * @return GameType|null
     */
    public static function fromShort($short) {
        return self::fromX(function (GameType $gameType) use ($short) {
            return strtolower($gameType->getShort()) == strtolower($short);
        });
    }

    /**
     * @param $name
     * @return GameType|null
     */
    public static function fromName($name) {
        return self::fromX(function (GameType $gameType) use ($name) {
            return strtolower($gameType->getName()) == strtolower($name);
        });
    }

    /**
     * @param $name
     * @return GameType|null
     */
    public static function fromEnum($name) {
        return self::fromX(function (GameType $gameType) use ($name) {
            return strtolower($gameType->getEnum()) == strtolower($name);
        });
    }
}