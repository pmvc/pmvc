<?php

namespace PMVC;

use Exception;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_TestCase;

class UtilArrayTest extends PHPUnit_Framework_TestCase
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
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage Param1 should be array type.
     */
    public function testArrayReplaceWrongType()
    {
        try {
            $s = '';
            arrayReplace($s);
        } catch (Exception $e) {
            throw new PHPUnit_Framework_Error(
                $e->getMessage(),
                0,
                $e->getFile(),
                $e->getLine()
            );
        }
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
