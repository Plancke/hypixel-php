<?php

namespace Plancke\HypixelPHP\color;

/**
 * Class ColorParser
 * @package Plancke\HypixelPHP\color
 */
class ColorParser {

    const DEFAULT_COLOR_HEX_MAP = [
        ColorUtils::BLACK => '#000000',
        ColorUtils::DARK_BLUE => '#0000AA',
        ColorUtils::DARK_GREEN => '#008000',
        ColorUtils::DARK_AQUA => '#00AAAA',
        ColorUtils::DARK_RED => '#AA0000',
        ColorUtils::DARK_PURPLE => '#AA00AA',
        ColorUtils::GOLD => '#FFAA00',
        ColorUtils::GRAY => '#AAAAAA',
        ColorUtils::DARK_GRAY => '#555555',
        ColorUtils::BLUE => '#5555FF',
        ColorUtils::GREEN => '#3CE63C',
        ColorUtils::AQUA => '#3CE6E6',
        ColorUtils::RED => '#FF5555',
        ColorUtils::LIGHT_PURPLE => '#FF55FF',
        ColorUtils::YELLOW => '#FFFF55',
        ColorUtils::WHITE => '#FFFFFF'
    ];
    const DEFAULT_FORMATTING_CSS = [
        ColorUtils::BOLD => 'font-weight: bold;',
        ColorUtils::STRIKETHROUGH => 'text-decoration: line-through;',
        ColorUtils::UNDERLINE => 'text-decoration: underline;',
        ColorUtils::ITALIC => 'font-style: italic;'
    ];

    /**
     * Parses MC encoded colors to HTML
     *
     * @param $string
     * @return string
     */
    public function parse($string) {
        $explodedString = $this->explodeColoredString($string);
        if ($explodedString == null) return null;

        return $this->handleExploded($explodedString);
    }

    /**
     * Explode a string into an array of segments for the ColorParser
     *
     * @param $string
     * @return array|null
     */
    protected function explodeColoredString($string) {
        if ($string == null || strlen($string) == 0) return null;

        $actualExplode = [];

        $explosion = explode(ColorUtils::COLOR_CHAR, $string);
        foreach ($explosion as $part) {
            if (strlen($explosion[0]) != 0 && sizeof($actualExplode) == 0) {
                array_push($actualExplode, [[
                    'code' => null,
                    'part' => $part
                ]]);
                continue;
            }

            if (strlen($part) == 0) {
                continue;
            }

            $code = ColorUtils::COLOR_CHAR . strtolower(substr($part, 0, 1));

            // new part
            if (array_search($code, ColorUtils::getAllColors())) {
                array_push($actualExplode, []);
            } else if ($code == ColorUtils::RESET) {
                array_push($actualExplode, []);
            }

            // append to last grouping
            array_push($actualExplode[sizeof($actualExplode) - 1], [
                'code' => $code,
                'part' => strlen($part) > 1 ? substr($part, 1) : null
            ]);
        }

        return $actualExplode;
    }

    /**
     * @param array $segments
     * @return string
     */
    protected function handleExploded($segments) {
        $out = '';
        foreach ($segments as $segment) {
            $out .= $this->handleSegment($segment);
        }
        return $out;
    }

    /**
     * @param array $parts
     * @return string
     */
    protected function handleSegment($parts) {
        $out = '';
        foreach (array_reverse($parts, true) as $part) {
            $out = $this->handlePart($part['code'], $part['part'] . $out);
        }
        return $out;
    }

    /**
     * @param string $code
     * @param string $part
     * @return string
     */
    protected function handlePart($code, $part) {
        if (in_array($code, ColorUtils::getAllFormattingCodes())) {
            return $this->_handleFormatting($code, $part);
        } else if (in_array($code, ColorUtils::getAllColors())) {
            return $this->_handleColor($code, $part);
        } else {
            return $part;
        }
    }

    /**
     * @param string $code
     * @param string $part
     * @return string
     */
    protected function _handleFormatting($code, $part) {
        $css = self::DEFAULT_FORMATTING_CSS[$code];
        return "<span style='$css'>" . $part . "</span>";
    }

    /**
     * @param string $color
     * @param string $part
     * @return string
     */
    protected function _handleColor($color, $part) {
        $color = self::DEFAULT_COLOR_HEX_MAP[$color];
        return "<span style='color: $color'>" . $part . "</span>";
    }

}