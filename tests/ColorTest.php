<?php

namespace Plancke\Tests;

use Plancke\HypixelPHP\color\ColorUtils;

class ColorTest extends \PHPUnit_Framework_TestCase {

    function testStripColor() {
        $colored = "§4Colored §OString§r";
        $unColored = "Colored String";
        $this->assertTrue(ColorUtils::stripColors($colored) == $unColored);
    }
}