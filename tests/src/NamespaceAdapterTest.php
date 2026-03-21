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
        $this->assertFalse($class->isCallable('xxx'));
        $this->willThrow(
            function () use ($class) {
                $class->xxx();
            },
            true,
            'Exception',
            'Function not found'
        );
    }
}
