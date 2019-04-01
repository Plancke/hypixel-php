<?php

namespace Plancke\Tests;

use PHPUnit\Framework\TestCase;
use Plancke\HypixelPHP\color\ColorUtils;

class ColorTest extends TestCase {

    function testStripColor() {
        $colored = "§4Colored §OString§r";
        $unColored = "Colored String";
        $this->assertTrue(ColorUtils::stripColors($colored) == $unColored);
    }
}