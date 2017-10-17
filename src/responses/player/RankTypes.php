<?php

namespace Plancke\HypixelPHP\responses\player;

abstract class RankTypes {

    const NON_DONOR = 1;
    const VIP = 2;
    const VIP_PLUS = 3;
    const MVP = 4;
    const MVP_PLUS = 5;

    const ADMIN = 100;
    const MODERATOR = 90;
    const HELPER = 80;
    const JR_HELPER = 70;
    const YOUTUBER = 60;

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
    public static function getDonorRanks() {
        return [
            self::NON_DONOR,
            self::VIP,
            self::VIP_PLUS,
            self::MVP,
            self::MVP_PLUS
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
     * @deprecated
     */
    public static function getAllTypes() {
        return self::values();
    }

    /**
     * @return array
     */
    public static function values() {
        return array_merge(self::getDonorRanks(), self::getStaffRanks());
    }

    /**
     * @param $id
     *
     * @return Rank|null
     */
    public static function fromID($id) {
        switch ($id) {
            case RankTypes::NON_DONOR:
                return new Rank(RankTypes::NON_DONOR, 'NON_DONOR', [
                    'prefix' => '§7',
                    'color' => '§7'
                ]);
            case RankTypes::VIP:
                return new Rank(RankTypes::VIP, 'VIP', [
                    'prefix' => '§a[VIP]',
                    'color' => '§a',
                    'eulaMultiplier' => 2
                ]);
            case RankTypes::VIP_PLUS:
                return new Rank(RankTypes::VIP_PLUS, 'VIP_PLUS', [
                    'prefix' => '§a[VIP§6+§a]',
                    'color' => '§a',
                    'eulaMultiplier' => 3
                ]);
            case RankTypes::MVP:
                return new Rank(RankTypes::MVP, 'MVP', [
                    'prefix' => '§b[MVP]',
                    'color' => '§b',
                    'eulaMultiplier' => 4
                ]);
            case RankTypes::MVP_PLUS:
                return new Rank(RankTypes::MVP_PLUS, 'MVP_PLUS', [
                    'prefix' => '§b[MVP§c+§b]',
                    'color' => '§b',
                    'eulaMultiplier' => 5
                ]);
            case RankTypes::YOUTUBER:
                return new Rank(RankTypes::YOUTUBER, 'YOUTUBER', [
                    'prefix' => '§6[YT]',
                    'color' => '§6',
                    'eulaMultiplier' => 7
                ]);
            case RankTypes::JR_HELPER:
                return new Rank(RankTypes::JR_HELPER, 'JR_HELPER', [
                    'prefix' => '§9[JR HELPER]',
                    'color' => '§9'
                ], true);
            case RankTypes::HELPER:
                return new Rank(RankTypes::HELPER, 'HELPER', [
                    'prefix' => '§9[HELPER]',
                    'color' => '§9'
                ], true);
            case RankTypes::MODERATOR:
                return new Rank(RankTypes::MODERATOR, 'MODERATOR', [
                    'prefix' => '§2[MOD]',
                    'color' => '§2'
                ], true);
            case RankTypes::ADMIN:
                return new Rank(RankTypes::ADMIN, 'ADMIN', [
                    'prefix' => '§c[ADMIN]',
                    'color' => '§c'
                ], true);
            default:
                return null;
        }
    }
}