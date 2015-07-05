<?php
namespace PMVC;
class HashMapTest extends \PHPUnit_Framework_TestCase
{
    function testHashMap()
    {
        $hash = new HashMap();
        $this->assertEquals('PMVC\HashMap',get_class($hash));
    }

    function testThis()
    {
        $hash = new FakeHash();
        $key = 'aaa';
        $value = 'bbb';
        $hash->mySet($key,$value);
        $this->assertEquals($value,$hash[$key]);
    }

    function testPlug()
    {
        $class = __NAMESPACE__.'\FakePlug';
        $plug_name = 'fake_plug';
        $plug = plug($plug_name,array(
            _CLASS=>$class
        ));
        $this->assertEquals($class,plug($plug_name)[_CLASS]);
    }
}

class FakeHash extends HashMap
{
    public function mySet($k,$v){
        $this[$k]=$v;
    }
}

class FakePlug extends PlugIn
{
}