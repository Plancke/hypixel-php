<?php

namespace Plancke\Tests;

use PHPUnit\Framework\TestCase;
use Plancke\HypixelPHP\classes\DataHolding;

class DataHoldingTest extends TestCase {


    function test() {
        $resource = new DataHoldingImpl([
            'int' => 1,
            'double' => 0.5,
            'array' => []
        ]);

        $this->assertTrue($resource->getDouble('double') == 0.5);
        $this->assertTrue($resource->getNumber('int') == 1);

        // not an array, fall back to default
        $this->assertTrue($resource->getArray('int') == []);
    }

}

class DataHoldingImpl {

    use DataHolding;

    protected $data;

    /**
     * Resource constructor.
     * @param $data
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

}