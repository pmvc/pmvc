<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugRefTest extends PHPUnit_Framework_TestCase
{
    public function testGeneralVerb()
    {
        $actual = 123;
        $expected = 456;
        \PMVC\ref($actual, '456');
        $this->assertEquals($expected, $actual);
    }

    public function testHashmap()
    {
        $h = new Hashmap([
            'a' => 123,
        ]);
        $a = &\PMVC\ref($h->a);
        $a = 456;
        $this->assertEquals(456, $h['a']);
        \PMVC\ref($h->a, 789);
        $this->assertEquals(789, $h['a']);
        \PMVC\ref($a, 101112);
        $this->assertEquals(101112, $h['a']);
    }
}
