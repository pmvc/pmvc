<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugArrayTest extends PHPUnit_Framework_TestCase
{
    public function testHasKeyString()
    {
        $this->assertTrue(hasKey('foo', 'foo'));
        $this->assertFalse(hasKey('foo', 'bar'));
    }

    public function testHasKeyArray()
    {
        $arr = ['foo'=>'foo-value'];
        $this->assertTrue(hasKey($arr, 'foo'));
        $this->assertFalse(hasKey($arr, 'bar'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Param1 need be an array.
     */
    public function testArrayReplaceWrongType()
    {
        $s = '';
        arrayReplace($s);
    }

    public function testArrayReplaceWithNull()
    {
        $a = ['foo'];
        $expected = ['bar'];
        $actual = arrayReplace($a, null, ['bar']);
        $this->assertEquals($expected, $actual);
    }

    public function testArrayReplaceWithString()
    {
        $a = ['foo'];
        $expected = ['foo', 'bar'];
        $actual = arrayReplace($a, 'bar');
        $this->assertEquals($expected, $actual);
    }

    public function testToArrayWithNull()
    {
        $expected = [];
        $actual = toArray(null);
        $this->assertEquals($expected, $actual);
    }

    public function testToArrayWithString()
    {
        $s = 'foo';
        $expected = [$s];
        $actual = toArray($s);
        $this->assertEquals($expected, $actual);
    }

    public function testToArrayWithArray()
    {
        $a = ['foo'];
        $actual = toArray($a);
        $this->assertEquals($a, $actual);
    }
}
