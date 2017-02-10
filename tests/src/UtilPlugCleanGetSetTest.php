<?php

namespace PMVC;

use stdClass;

class UtilPlugCleanGetSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * handle illegal offset type in isset or empty.
     */
     public function testHandleGetObjectKey()
     {
         $k = new stdClass();
         $arr = ['foo'=>'bar'];
         $actual = \PMVC\get($arr, $k);
         $this->assertNull($actual);
         $arr2 = new FakeHashMap();
         $actual2 = \PMVC\get($arr2, $k);
         $this->assertEquals($k, $actual2);
     }

    /**
     * Test set object.
     */
     public function testSetObject()
     {
         $arr = (object) [1, 2, 3];
         $arr1 = [];
         \PMVC\set($arr1, $arr);
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
         \PMVC\set($arr1, $arr);
         $expected = [1, 2, 3];
         $this->assertEquals($expected, $arr1);
     }

    /**
     * @function clean
     */
    public function testCleanKeepInArray()
    {
        $arr = [1, 2, 3];
        \PMVC\clean($arr);
        $expected = [];
        $this->assertEquals($expected, $arr);
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
