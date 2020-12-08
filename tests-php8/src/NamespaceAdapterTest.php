<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class NamespaceAdapterTest extends PHPUnit_Framework_TestCase
{
    public function testCall()
    {
        $class = new NamespaceAdapter('PMVC');
        $actual = $class->splitDir('/abc:/def');
        $expected = [
            '/abc',
            '/def',
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testFunctionNotExists()
    {
        $class = new NamespaceAdapter('PMVC');
        $actual = $class->xxx();
        $this->assertNull($actual);
        $this->assertNull($class->isCallable('xxx'));
    }
}
