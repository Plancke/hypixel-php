<?php

namespace Plancke\HypixelPHP\responses\player;

/**
 * Class RankTypes
 * @package Plancke\HypixelPHP\responses\player
 */
abstract class RankTypes {

    const NON_DONOR = 1;
    const VIP = 2;
    const VIP_PLUS = 3;
    const MVP = 4;
    const MVP_PLUS = 5;
    const SUPERSTAR = 6;

    const ADMIN = 100;
    const MODERATOR = 90;
    const HELPER = 80;
    const JR_HELPER = 70;
    const YOUTUBER = 60;

    protected static $cache = [];

    public static function fromName($db) {
        foreach (RankTypes::values() as $id) {
            $rank = RankTypes::fromID($id);
            if ($rank != null) {
                if ($rank->getName() == $db) {
                    return $rank;
                }
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public static function values() {
        return [
            self::NON_DONOR,
            self::VIP,
            self::VIP_PLUS,
            self::MVP,
            self::MVP_PLUS,
            self::SUPERSTAR,
            self::ADMIN,
            self::MODERATOR,
            self::HELPER,
            self::JR_HELPER,
            self::YOUTUBER
        ];
    }

    /**
     * @return array
     */
    public static function getDonorRanks() {
        return [
            self::NON_DONOR,
            self::VIP,
            self::VIP_PLUS,
            self::MVP,
            self::MVP_PLUS,
            self::SUPERSTAR
        ];
    }

    /**
     * @return array
     */
    public static function getStaffRanks() {
        return [
            self::ADMIN,
            self::MODERATOR,
            self::HELPER,
            self::JR_HELPER,
            self::YOUTUBER
        ];
    }

    /**
     * @param $id
     *
     * @return Rank|null
     */
    public static function fromID($id) {
        if (!isset(RankTypes::$cache[$id])) {
            $rank = null;
            switch ($id) {
                case RankTypes::NON_DONOR:
                    $rank = new Rank(RankTypes::NON_DONOR, 'NON_DONOR', [
                        'prefix' => '§7',
                        'color' => '§7'
                    ]);
                    break;
                case RankTypes::VIP:
                    $rank = new Rank(RankTypes::VIP, 'VIP', [
                        'prefix' => '§a[VIP]',
                        'color' => '§a',
                        'eulaMultiplier' => 2
                    ]);
                    break;
                case RankTypes::VIP_PLUS:
                    $rank = new Rank(RankTypes::VIP_PLUS, 'VIP_PLUS', [
                        'prefix' => '§a[VIP§6+§a]',
                        'color' => '§a',
                        'eulaMultiplier' => 3
                    ]);
                    break;
                case RankTypes::MVP:
                    $rank = new Rank(RankTypes::MVP, 'MVP', [
                        'prefix' => '§b[MVP]',
                        'color' => '§b',
                        'eulaMultiplier' => 4
                    ]);
                    break;
                case RankTypes::MVP_PLUS:
                    $rank = new Rank(RankTypes::MVP_PLUS, 'MVP_PLUS', [
                        'prefix' => '§b[MVP§c+§b]',
                        'color' => '§b',
                        'eulaMultiplier' => 5
                    ]);
                    break;
                case RankTypes::SUPERSTAR:
                    $rank = new Rank(RankTypes::SUPERSTAR, 'SUPERSTAR', [
                        'prefix' => '§6[MVP§c++§6]',
                        'color' => '§6'
                    ]);
                    break;
                case RankTypes::YOUTUBER:
                    $rank = new Rank(RankTypes::YOUTUBER, 'YOUTUBER', [
                        'prefix' => '§c[§fYOUTUBE§c]',
                        'color' => '§c',
                        'eulaMultiplier' => 7
                    ]);
                    break;
                case RankTypes::JR_HELPER:
                    $rank = new Rank(RankTypes::JR_HELPER, 'JR_HELPER', [
                        'prefix' => '§9[JR HELPER]',
                        'color' => '§9'
                    ], true);
                    break;
                case RankTypes::HELPER:
                    $rank = new Rank(RankTypes::HELPER, 'HELPER', [
                        'prefix' => '§9[HELPER]',
                        'color' => '§9'
                    ], true);
                    break;
                case RankTypes::MODERATOR:
                    $rank = new Rank(RankTypes::MODERATOR, 'MODERATOR', [
                        'prefix' => '§2[MOD]',
                        'color' => '§2'
                    ], true);
                    break;
                case RankTypes::ADMIN:
                    $rank = new Rank(RankTypes::ADMIN, 'ADMIN', [
                        'prefix' => '§c[ADMIN]',
                        'color' => '§c'
                    ], true);
                    break;
            }

            RankTypes::$cache[$id] = $rank;
        }

        return RankTypes::$cache[$id];
    }

    /**
     * @deprecated
     */
    public static function getAllTypes() {
        return self::values();
    }
}