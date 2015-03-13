<?php

/**
 * Add a Minecraft color coded string to an image.
 * @param $img
 * @param $font
 * @param $fontSize
 * @param $startX
 * @param $startY
 * @param $string
 */
function addMCColorString(&$img, $font, $fontSize, $startX, $startY, $string)
{
    $MCColors = array(
        "0" => "#000000",
        "1" => "#0000AA",
        "2" => "#008000",
        "3" => "#00AAAA",
        "4" => "#AA0000",
        "5" => "#AA00AA",
        "6" => "#FFAA00",
        "7" => "#AAAAAA",
        "8" => "#555555",
        "9" => "#5555FF",
        "a" => "#3CE63C",
        "b" => "#55FFFF",
        "c" => "#FF5555",
        "d" => "#FF55FF",
        "e" => "#FFFF55",
        "f" => "#FFFFFF"
    );

    if (strpos($string, "§") === false) {
        $string = '§7' . $string;
    }

    $currentX = $startX;
    $currentY = $startY + 16;
    foreach (explode("§", $string) as $part) {
        $rgb = hex2rgb($MCColors[substr($part, 0, 1)]);
        $color = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);

        $part = substr($part, 1);
        $bbox = imagettfbbox($fontSize, 0, $font, $part);
        imagettftext($img, $fontSize, 0, $currentX, $currentY, $color, $font, $part);
        $currentX += ($bbox[4] - $bbox[0]);
    }
}

/**
 * Converts Hexadecimal color code into RGB
 * @param $hex
 * @return array
 */
function hex2rgb($hex)
{
    $hex = str_replace("#", "", $hex);

    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    return [$r, $g, $b];
}