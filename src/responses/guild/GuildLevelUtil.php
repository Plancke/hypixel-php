<?php

namespace Plancke\HypixelPHP\responses\guild;


class GuildLevelUtil {

    const EXP_NEEDED = [
        100000,
        150000,
        250000,
        500000,
        750000,
        1000000,
        1250000,
        1500000,
        2000000,
        2500000,
        2500000,
        2500000,
        2500000,
        2500000,
        3000000
    ];

    public static function getLevel($exp) {
        $level = 0;

        for ($i = 0; ; $i++) {
            $need = $i >= sizeof(GuildLevelUtil::EXP_NEEDED) ? GuildLevelUtil::EXP_NEEDED[sizeof(GuildLevelUtil::EXP_NEEDED) - 1] : GuildLevelUtil::EXP_NEEDED[$i];
            $exp -= $need;
            if ($exp < 0) {
                return $level;
            } else {
                $level++;
            }
        }

        // should never happen
        return -1;
    }

}
