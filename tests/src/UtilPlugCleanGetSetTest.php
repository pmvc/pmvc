<?php

namespace PMVC;

class UtilPlugCleanGetSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test set object.
     */
     public function testSetObject()
     {
        $arr = (object)[1, 2, 3];
        $arr1 = [];
        \PMVC\set($arr1,$arr);
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
