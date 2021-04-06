<?php

namespace PMVC;

class UtilArrayTest extends TestCase
{
    public function testHasKeyString()
    {
        $this->assertTrue(hasKey('foo', 'foo'));
        $this->assertFalse(hasKey('foo', 'bar'));
    }

    public function testHasKeyArray()
    {
        $arr = ['foo' => 'foo-value'];
        $this->assertTrue(hasKey($arr, 'foo'));
        $this->assertFalse(hasKey($arr, 'bar'));
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Param1 should be array type.
     */
    public function testArrayReplaceWrongType()
    {
        $this->willThrow(function () {
            $s = '';
            arrayReplace($s);
        });
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
}
