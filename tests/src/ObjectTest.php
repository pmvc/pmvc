<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class ObjectTest extends PHPUnit_Framework_TestCase
{
    function testSetValue()
    {
        $o = new Object();
        $expected = 'foo';
        $o($expected);
        $actual = $o();
        $this->assertEquals($expected, $actual);
    }
}
