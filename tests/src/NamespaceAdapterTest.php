<?php

namespace PMVC;

class NamespaceAdapterTest extends TestCase
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
