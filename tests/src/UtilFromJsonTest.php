<?php

namespace PMVC;

class UtilFromJsonTest extends TestCase
{
    public function testIsNotJsonString()
    {
        $a = [];
        $b = \PMVC\fromJson($a);
        $this->assertEquals($a, $b);
    }

    public function testParseJsonSuccess()
    {
        $a = '{}';
        $b = \PMVC\fromJson($a);
        $this->assertEquals((object) [], $b);
    }

    public function testParseJsonFailed()
    {
        $a = '{';
        $b = \PMVC\fromJson($a);
        $this->assertEquals($a, $b);
    }
}
