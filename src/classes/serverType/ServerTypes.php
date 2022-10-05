<?php

namespace Plancke\HypixelPHP\classes\serverType;

use Closure;

/**
 * Class GameTypes
 * @package Plancke\HypixelPHP\classes\serverType
 */
class ServerTypes {
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
    const REPLAY = 65;
    const SMP = 67;
    const WOOL_GAMES = 68;

    const MAIN = -1;

    /**
     * @param $db
     * @return ServerType|null
     */
    public static function fromDbName($db) {
        return self::fromX(function (ServerType $serverType) use ($db) {
            return strtolower($serverType->getDb()) == strtolower($db);
        });
    }

    /**
     * @param Closure $test
     * @return ServerType|null
     */
    public static function fromX(Closure $test) {
        foreach (self::values() as $id) {
            $serverType = self::fromID($id);
            if ($serverType != null) {
                if ($test($serverType)) {
                    return $serverType;
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
            self::REPLAY,
            self::SMP,
            self::WOOL_GAMES,

            self::MAIN
        ];
    }

    /**
     * @param $id
     *
     * @return GameType|ServerType|null
     */
    public static function fromID($id) {
        switch ($id) {
            case self::QUAKE:
                return new GameType('QUAKE', 'Quake', 'Quake', 'Quake', self::QUAKE, false);
            case self::WALLS:
                return new GameType('WALLS', 'Walls', 'Walls', 'Walls', self::WALLS, false);
            case self::PAINTBALL:
                return new GameType('PAINTBALL', 'Paintball', 'Paintball', 'Paintball', self::PAINTBALL, false);
            case self::SURVIVAL_GAMES:
                return new GameType('SURVIVAL_GAMES', 'HungerGames', 'Blitz Survival Games', 'BSG', self::SURVIVAL_GAMES);
            case self::TNTGAMES:
                return new GameType('TNTGAMES', 'TNTGames', 'TNT Games', 'TNT Games', self::TNTGAMES);
            case self::VAMPIREZ:
                return new GameType('VAMPIREZ', 'VampireZ', 'VampireZ', 'VampireZ', self::VAMPIREZ, false);
            case self::WALLS3:
                return new GameType('WALLS3', 'Walls3', 'Mega Walls', 'MW', self::WALLS3);
            case self::ARCADE:
                return new GameType('ARCADE', 'Arcade', 'Arcade', 'Arcade', self::ARCADE);
            case self::ARENA:
                return new GameType('ARENA', 'Arena', 'Arena Brawl', 'Arena', self::ARENA, false);
            case self::UHC:
                return new GameType('UHC', 'UHC', 'UHC Champions', 'UHC', self::UHC);
            case self::MCGO:
                return new GameType('MCGO', 'MCGO', 'Cops and Crims', 'CaC', self::MCGO);
            case self::BATTLEGROUND:
                return new GameType('BATTLEGROUND', 'Battleground', 'Warlords', 'Warlords', self::BATTLEGROUND);
            case self::SUPER_SMASH:
                return new GameType('SUPER_SMASH', 'SuperSmash', 'Smash Heroes', 'Smash Heroes', self::SUPER_SMASH);
            case self::GINGERBREAD:
                return new GameType('GINGERBREAD', 'GingerBread', 'Turbo Kart Racers', 'TKR', self::GINGERBREAD, false);
            case self::HOUSING:
                return new GameType('HOUSING', 'Housing', 'Housing', 'Housing', self::HOUSING, false);
            case self::SKYWARS:
                return new GameType('SKYWARS', 'SkyWars', 'SkyWars', 'SkyWars', self::SKYWARS);
            case self::TRUE_COMBAT:
                return new GameType('TRUE_COMBAT', 'TrueCombat', 'Crazy Walls', 'Crazy Walls', self::TRUE_COMBAT);
            case self::SPEED_UHC:
                return new GameType('SPEED_UHC', 'SpeedUHC', 'Speed UHC', 'Speed UHC', self::SPEED_UHC);
            case self::SKYCLASH:
                return new GameType('SKYCLASH', 'SkyClash', 'SkyClash', 'SkyClash', self::SKYCLASH);
            case self::LEGACY:
                return new GameType('LEGACY', 'Legacy', 'Classic Games', 'Classic', self::LEGACY);
            case self::PROTOTYPE:
                return new GameType('PROTOTYPE', 'Prototype', 'Prototype', 'Prototype', self::PROTOTYPE, false);
            case self::BEDWARS:
                return new GameType('BEDWARS', 'Bedwars', 'Bed Wars', 'Bed Wars', self::BEDWARS, false);
            case self::MURDER_MYSTERY:
                return new GameType('MURDER_MYSTERY', 'MurderMystery', 'Murder Mystery', 'Murder Mystery', self::MURDER_MYSTERY, false);
            case self::BUILD_BATTLE:
                return new GameType('BUILD_BATTLE', 'BuildBattle', 'Build Battle', 'Build Battle', self::BUILD_BATTLE, false);
            case self::DUELS:
                return new GameType('DUELS', 'Duels', 'Duels', 'Duels', self::DUELS, false);
            case self::SKYBLOCK:
                return new GameType('SKYBLOCK', 'SkyBlock', 'SkyBlock', 'SkyBlock', self::SKYBLOCK, false);
            case self::PIT:
                return new GameType('PIT', 'Pit', 'Pit', 'Pit', self::PIT, false);
            case self::REPLAY:
                return new GameType('REPLAY', 'Replay', 'Replay', 'Replay', self::REPLAY, false);
            case self::SMP:
                return new GameType('SMP', 'SMP', 'SMP', 'SMP', self::SMP, false);
            case self::WOOL_GAMES:
                return new GameType('WOOL_GAMES', 'WoolGames', 'Wool Games', 'Wool Games', self::WOOL_GAMES, false);

            # Lobby Types
            case self::MAIN:
                return new LobbyType('MAIN', 'MainLobby', 'Main Lobby', 'Main Lobby', self::MAIN);
        }
        return null;
    }

    /**
     * @param $short
     * @return ServerType|null
     */
    public static function fromShort($short) {
        return self::fromX(function (ServerType $serverType) use ($short) {
            return strtolower($serverType->getShort()) == strtolower($short);
        });
    }

    /**
     * @param $name
     * @return ServerType|null
     */
    public static function fromName($name) {
        return self::fromX(function (ServerType $serverType) use ($name) {
            return strtolower($serverType->getName()) == strtolower($name);
        });
    }

    /**
     * @param $name
     * @return ServerType|null
     */
    public static function fromEnum($name) {
        return self::fromX(function (ServerType $serverType) use ($name) {
            return strtolower($serverType->getEnum()) == strtolower($name);
        });
    }
}