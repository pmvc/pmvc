<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugToArrayTest extends PHPUnit_Framework_TestCase
{
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

    public function testToArrayWithObject()
    {
        $a = ['foo'];
        $actual = toArray((object) $a);
        $this->assertEquals($a, $actual);
    }

    public function testToArrayWithHashMap()
    {
        $a = ['foo', 'bar'];
        $hash = new HashMap($a);
        $actual = toArray($hash);
        $this->assertEquals($a, $actual);
    }

    public function testToArrayOnlyValues()
    {
        $a = ['a'=>'foo', 'b'=>'bar'];
        $hash = new HashMap($a);
        $actual = toArray($hash, true);
        $expedted = ['foo', 'bar'];
        $this->assertEquals($expedted, $actual);
    }
}
