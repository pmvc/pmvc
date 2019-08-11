<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class HashMapTest extends PHPUnit_Framework_TestCase
{
    public function testHashMap()
    {
        $hash = new HashMap();
        $this->assertEquals('PMVC\HashMap', get_class($hash));
    }

    public function testThis()
    {
        $hash = new FakeHash();
        $key = 'aaa';
        $value = 'bbb';
        $hash->mySet($key, $value);
        $this->assertEquals($value, $hash[$key]);
    }

    public function testRefObject()
    {
        $hash = new HashMap();
        $hash['abc'] = 123;
        $abc = $hash->abc;
        $abc_1 = &$abc();
        $new_value = 456;
        $abc_1 = $new_value;
        $this->assertEquals($new_value, $hash['abc']);
    }

    public function testRefArray()
    {
        $hash = new HashMap();
        $hash['abc'] = 123;
        $abc = &$hash['abc'];
        $new_value = 789;
        $abc = $new_value;
        $this->assertEquals($new_value, $hash['abc']);
    }

    public function testPlug()
    {
        $class = __NAMESPACE__.'\FakePlugIn';
        $plug_name = 'fake_plug';
        $plug = plug(
            $plug_name, [
            _CLASS => $class,
            ]
        );
        $this->assertEquals($class, plug($plug_name)[_CLASS]);
    }

    public function testGetAll()
    {
        $arr = ['a' => '111', 'b' => '222'];
        $hash = new HashMap($arr);
        $this->assertEquals($arr, get($hash));
    }

    public function testKeyset()
    {
        $arr = ['a' => '111'];
        $hash = new HashMap($arr);
        $this->assertEquals(['a'], $hash->keySet());
    }

    public function testOffsetExists()
    {
        $arr = ['a' => '111'];
        $hash = new HashMap($arr);
        $this->assertTrue(isset($hash['a']));
        $this->assertFalse(isset($hash['b']));
    }

    public function testOffsetGet()
    {
        $arr = ['a' => '111'];
        $hash = new HashMap($arr);
        $this->assertEquals($arr['a'], $hash['a']);
        $a = $hash->a;
        $this->assertEquals($arr['a'], $a());
    }

    public function testOffsetSet()
    {
        $hash = new HashMap();
        $new_value = '111';
        $hash['a'] = $new_value;
        $newb_value = '222';
        $hash->b = $newb_value;
        $this->assertEquals($new_value, $hash['a']);
        $this->assertEquals($newb_value, $hash['b']);
    }

    public function testOffsetUnset()
    {
        $arr = ['a' => '111', 'b' => '222'];
        $hash = new HashMap($arr);
        $this->assertTrue(isset($hash['a']));
        unset($hash['a']);
        $this->assertFalse(isset($hash['a']));
        $this->assertTrue(isset($hash['b']));
        $hash->offsetUnset();
        $this->assertFalse(isset($hash['b']));
    }

    public function testUnsetObject()
    {
        $hash = new HashMap();
        $hash->a = 1;
        $this->assertTrue(isset($hash->a));
        unset($hash->a);
        $this->assertFalse(isset($hash->a));
    }

    public function testAppend()
    {
        $arr = [
            'a' => ['aaa' => 111],
        ];
        $hash = new HashMap($arr);
        $newSet = [
            'a' => ['bbb' => 222],
        ];
        $hash[$newSet] = null;
        $this->assertEquals($newSet, get($hash));
        $hash[[]] = [
            'a' => ['ccc' => 333],
        ];
        $expected = $newSet;
        $expected['a']['ccc'] = 333;
        $this->assertEquals($expected, get($hash));
        $hash[] = ['ddd'];
        $this->assertEquals(['ddd'], $hash[0]);
    }

    public function testHashMapWalk()
    {
        $arr = ['foo'=>['a'], 'bar'];
        $list = new HashMap($arr, true);
        $expected = new HashMap(['foo'=>new HashMap(['a']), 'bar']);
        $this->assertEquals($expected, $list);
    }
}

class FakeHash extends HashMap
{
    public function mySet($k, $v)
    {
        $this[$k] = $v;
    }
}
