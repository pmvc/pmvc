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

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Function not found 
     */
    public function testFunctionNotExists()
    {
        $class = new NamespaceAdapter('PMVC');
        $this->assertFalse($class->isCallable('xxx'));
        $this->willThrow(
            function() use ($class) {
                $class->xxx();
            }
        );
    }
}
