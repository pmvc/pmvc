<?php

namespace PMVC;

use stdClass;

class UtilPlugCleanGetSetTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMultiValueWithArray()
    {
        $a = ['a', 'b', 'c'];
        $this->assertEquals(['b', 'c'], array_merge([], get($a, [1, 2])));
    }

    public function testGetMultiValueWithObject()
    {
        $a = (object) ['a'=>1, 'b'=>2, 'c'=>3];
        $this->assertEquals(['a'=>1, 'b'=>2], get($a, ['a', 'b']));
    }

    public function testGetMultiValueWithInvalidKey()
    {
        $key = [new Object(), 'a', 'b', null, false];
        $arr = ['a'=>'foo', 'b'=>'bar', null=>'null', false=>'false'];
        $this->assertEquals($arr, get($arr, $key), 'Test get array with invalid key');
        $obj = (object) ['a'=>'foo', 'b'=>'bar'];
        $this->assertEquals(
            (array) $obj,
            get($obj, $key),
            'Test get array with invalid key'
        );
    }

    public function testGetDefaultValueWithArrayAccess()
    {
        $a = new HashMap();
        $result = get($a, 'foo', 'bar');
        $this->assertEquals('bar', $result);
    }

    /**
     * handle illegal offset type in isset or empty.
     */
    public function testHandleGetObjectKey()
    {
        $k = new stdClass();
        $arr = ['foo'=>'bar'];
        $actual = get($arr, $k);
        $this->assertNull($actual);
        $arr2 = new FakeHashMap();
        $actual2 = get($arr2, $k);
        $this->assertEquals($k, $actual2);
    }

    public function testGetDefaultValueWithLaze()
    {
        $a = [];
        $expected = 'foo';
        $actual = get(
            $a,
            'bar',
            function () use ($expected) {
                return $expected;
            }
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test set object.
     */
    public function testSetObject()
    {
        $arr = (object) [1, 2, 3];
        $arr1 = [];
        set($arr1, $arr);
        $expected = [1, 2, 3];
        $this->assertEquals($expected, $arr1);
    }

    /**
     * Test set hashmap.
     */
    public function testSetHashmap()
    {
        $arr = new Hashmap([1, 2, 3]);
        $arr1 = [];
        set($arr1, $arr);
        $expected = [1, 2, 3];
        $this->assertEquals($expected, $arr1);
    }

    public function testSetNull()
    {
        $a = ['foo', 'bar'];
        $b = $a;
        set($a, null, null);
        $this->assertEquals($b, $a);
    }

    /**
     * @function clean
     */
    public function testCleanKeepInArray()
    {
        $arr = [1, 2, 3];
        clean($arr);
        $expected = [];
        $this->assertEquals($expected, $arr);
    }

    public function testCleanArrayAccess()
    {
        $a = new HashMap(['foo', 'bar']);
        $this->assertEquals(2, count($a));
        clean($a);
        $this->assertEquals(0, count($a));
    }

    public function testCleanByReplace()
    {
        $a = ['foo'];
        $b = ['bar'];
        clean($a, $b);
        $this->assertEquals($b, $a);
    }
}

class fakeHashMap extends HashMap
{
    public function &offsetGet($k)
    {
        if (!is_object($k)) {
            $k = null;
        }

        return $k;
    }
}
