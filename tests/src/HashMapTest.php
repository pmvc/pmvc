<?php
namespace PMVC;
class HashMapTest extends \PHPUnit_Framework_TestCase
{
    function testHashMap()
    {
        $hash = new HashMap();
        $this->assertEquals('PMVC\HashMap', get_class($hash));
    }

    function testThis()
    {
        $hash = new FakeHash();
        $key = 'aaa';
        $value = 'bbb';
        $hash->mySet($key, $value);
        $this->assertEquals($value, $hash[$key]);
    }

    function testRef()
    {
        $hash = new HashMap();
        $hash['abc'] = 123;
        $abc = $hash->abc;
        $abc_1 =& $abc();
        $new_value = 456;
        $abc_1 = $new_value;
        $this->assertEquals($new_value, $hash['abc']);
    }

    function testPlug()
    {
        $class = __NAMESPACE__.'\FakePlug';
        $plug_name = 'fake_plug';
        $plug = plug(
            $plug_name, array(
            _CLASS=>$class
            )
        );
        $this->assertEquals($class, plug($plug_name)[_CLASS]);
    }

    function testGetAll()
    {
        $arr = array('a'=>'111','b'=>'222');
        $hash = new HashMap($arr);
        $this->assertEquals($arr, \PMVC\get($hash));
    }

    function testKeyset()
    {
        $arr = array('a'=>'111');
        $hash = new HashMap($arr);
        $this->assertEquals(array('a'),$hash->keySet());
    }

    function testOffsetExists()
    {
        $arr = array('a'=>'111');
        $hash = new HashMap($arr);
        $this->assertTrue(isset($hash['a']));
        $this->assertFalse(isset($hash['b']));
    }

    function testOffsetGet()
    {
        $arr = array('a'=>'111');
        $hash = new HashMap($arr);
        $this->assertEquals($arr['a'],$hash['a']);
        $a = $hash->a;
        $this->assertEquals($arr['a'],$a());
    }

    function testOffsetSet()
    {
        $hash = new HashMap();
        $new_value = '111';
        $hash['a'] = $new_value;
        $newb_value = '222';
        $hash->b = $newb_value;
        $this->assertEquals($new_value,$hash['a']);
        $this->assertEquals($newb_value,$hash['b']);
    }

    function testOffsetUnset()
    {
        $arr = array('a'=>'111','b'=>'222');
        $hash = new HashMap($arr);
        $this->assertTrue(isset($hash['a']));
        unset($hash['a']);
        $this->assertFalse(isset($hash['a']));
        $this->assertTrue(isset($hash['b']));
        $hash->offsetUnset();
        $this->assertFalse(isset($hash['b']));
    }
}

class FakeHash extends HashMap
{
    public function mySet($k,$v)
    {
        $this[$k]=$v;
    }
}

