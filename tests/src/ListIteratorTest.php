<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class ListIteratorTest extends PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $arr = [1, 2];
        $list = new ListIterator($arr);
        foreach ($list as $k=>$v) {
            $this->assertEquals($arr[$k], $v);
        }
    }

    public function testCount()
    {
        $arr = [1, 2];
        $list = new ListIterator($arr);
        $this->assertEquals(2, count($list));
    }

    public function testIterator()
    {
        $arr = [1, 2];
        $list = new ListIterator($arr);
        $it = $list->getIterator();
        $it->next();
        $this->assertEquals(2, $it->current());
        $this->assertTrue($it->valid());
        $it->next();
        $this->assertFalse($it->valid());
        $it->rewind();
        $this->assertEquals(1, $it->current());
        $this->assertTrue($it->valid());
    }
}
