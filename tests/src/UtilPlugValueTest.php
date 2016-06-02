<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

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
}
