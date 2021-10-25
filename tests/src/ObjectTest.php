<?php

namespace PMVC;

class ObjectTest extends TestCase
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
