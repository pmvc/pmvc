<?php

namespace PMVC;

use PHPUnit_Framework_Error;
use PHPUnit_Framework_TestCase;
use TypeError;

class UtilPlugValueTest extends PHPUnit_Framework_TestCase
{
    public function testGetValue()
    {
        $expected = 'd';
        $arr = [
            'a' => [
                'b' => [
                    'c' => 'd',
                ],
            ],
        ];
        $actual = value($arr, ['a', 'b', 'c'], null);
        $this->assertEquals($expected, $actual);
    }

    public function testGetDefaultValue()
    {
        $expected = 'xxx';
        $arr = [ ];
        $actual = value($arr, ['a', 'b', 'c'], $expected);
        $this->assertEquals($expected, $actual);
    }

    public function testGetHashMap()
    {
        $h = new HashMap(['a', 'b' => ['c' => 'd']]);
        $actual = value($h, ['b', 'c']);
        $this->assertTrue(is_object($h));
        $this->assertEquals('d', $actual);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessageRegExp /(Argument 2 passed to PMVC\\value\(\) must be)/
     */
    public function testHandlePathIsNotArray()
    {
        $h = ['a', 'b'];
        try {
            value($h, 'not-path');
        } catch (TypeError $e) {
            throw new PHPUnit_Framework_Error(
                $e->getMessage(),
                0,
                $e->getFile(),
                $e->getLine()
            );
        }
    }
}
