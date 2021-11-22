<?php

namespace PMVC;

use TypeError;

class UtilValueTest extends TestCase
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

    public function testGetEmpty()
    {
        $a = null;
        $actual = value($a, [1]);
        $this->assertEquals(null, $actual);
    }

    public function testGetWithEmptyPath()
    {
        $a = ['foo'];
        $actual = value($a, []);
        $this->assertEquals($a, $actual);
    }

    public function testGetDefaultValue()
    {
        $expected = 'xxx';
        $arr = [ ];
        $actual = value($arr, ['a', 'b', 'c'], $expected);
        $this->assertEquals($expected, $actual);
    }

    public function testGetDefaultValueWithLaze()
    {
        $expected = 'xxx';
        $arr = [ ];
        $actual = value(
            $arr,
            ['a', 'b', 'c'],
            function () use ($expected) {
                return $expected;
            }
        );
        $this->assertEquals($expected, $actual);
    }

    public function testGetHashMap()
    {
        $h = new HashMap(['a', 'b' => ['c' => 'd']]);
        $actual = value($h, ['b', 'c']);
        $this->assertTrue(is_object($h));
        $this->assertEquals('d', $actual);
    }

    public function testSetObjectValue()
    {
        $a = (object) [
            'b',
        ];
        value($a, ['c', 'd'], (object) [], 'e');
        $this->assertEquals('e', $a->c->d);
    }

    public function testSetObjectValueWithAppend()
    {
        $a = (object) [
            'b',
        ];
        value($a, ['b', 'c'], (object) [], 'e', true);
        value($a, ['b', 'c'], (object) [], 'f', true);
        $this->assertEquals(['e', 'f'], $a->b->c);
    }

    /**
     * Test get a->xxx.
     */
    public function testGetObjectValue()
    {
        $expected = 'd';
        $a = (object) [
            'b' => (object) [
                'c' => $expected,
            ],
        ];
        $this->assertEquals($expected, $a->b->c);
        $this->assertEquals($expected, value($a, ['b', 'c']));
    }

    /**
     * @expectedException              TypeError
     * @expectedExceptionMessageRegExp /(Argument 2 passed to PMVC\\value\(\) must be)/
     */
    public function testHandlePathIsNotArray()
    {
        $h = ['a', 'b'];

        $this->willThrow(
            function () use ($h) {
                value($h, 'not-path');
            },
            'TypeError'
        );
    }
}
