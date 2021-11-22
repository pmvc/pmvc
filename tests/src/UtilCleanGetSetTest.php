<?php

namespace PMVC;

use stdClass;

class UtilCleanGetSetTest extends TestCase
{
    public function testGetAll()
    {
        $a = ['foo', 'bar'];
        $this->assertEquals($a, get($a), 'with array');
        $b = new HashMap($a);
        $this->assertEquals($a, get($b), 'with hashmap');
        $c = (object) $a;
        $this->assertEquals($a, get($c), 'with object convert');
        $d = new HashMap();
        $d->a = 'foo';
        $d->b = 'bar';
        $this->assertEquals(['a'=>'foo', 'b'=>'bar'], get($d), 'with object assign');
    }

    public function testGetAllWithToArray()
    {
        $arr = new FakeHashMap();
        $this->assertEquals([], get($arr));
    }

    public function testToArrayNotEffectOriginalMap()
    {
        $a = ['a'=>'foo', 'b'=>new HashMap(['bar'])];
        $b = new HashMap($a);
        $c = &get($b);
        $c['a'] = '000';
        $this->assertEquals($a['a'], $b['a']);
        $this->assertEquals($a['b'], $b['b']);
        $this->assertEquals('000', $c['a']);
        $this->assertEquals(['bar'], $c['b']);
    }

    public function testGetMultiValueWithArray()
    {
        $a = ['a', 'b', 'c'];
        $this->assertEquals(['b', 'c'], array_merge([], get($a, [1, 2])));
    }

    public function testGetMultiValueWithObject()
    {
        $a = (object) ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(['a' => 1, 'b' => 2], get($a, ['a', 'b']));
    }

    public function testGetMultiValueWithInvalidKey()
    {
        $key = [new BaseObject(), 'a', 'b', false, null, true];
        $arr = ['a' => 'foo', 'b' => 'bar', false => 'false', null => 'null',  true=> 'true'];
        $this->assertEquals($arr, get($arr, $key), 'Test get array with invalid key');
        $obj = (object) ['a' => 'foo', 'b' => 'bar'];
        $this->assertEquals(
            (array) $obj,
            get($obj, $key),
            'Test get object with invalid key'
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
        $arr = ['foo' => 'bar'];
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

    public function testSetAppend()
    {
        $a = ['foo'=>'a', 'bar'=>'b'];
        set($a, 'bar', 'aaa', true);
        set($a, 'bar', 'bbb', true);
        $this->assertEquals(['foo' => 'a', 'bar' => ['aaa', 'bbb']], $a);
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

    public function testCleanMulti()
    {
        $a = ['foo', 'bar'];
        $b = [0, 1];
        clean($a, $b);
        $this->assertTrue(empty($a));
    }
}

class fakeHashMap extends HashMap
{
    public function toArray()
    {
        return [];
    }

    public function &offsetGet($k = null)
    {
        if (!is_object($k)) {
            $k = null;
        }

        return $k;
    }
}
