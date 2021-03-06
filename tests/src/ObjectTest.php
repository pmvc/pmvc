<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class ObjectTest extends PHPUnit_Framework_TestCase
{
    public function testSetValue()
    {
        $o = new BaseObject();
        $expected = 'foo';
        $o($expected);
        $actual = $o();
        $this->assertEquals($expected, $actual);
    }
}
