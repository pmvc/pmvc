<?php

namespace PMVC;

class UtilStringSplitCamelCaseTest extends TestCase
{
    public function testCamelCase()
    {
        $expected = ['camel', 'case'];
        $this->assertEquals($expected, splitCamelCase('CamelCase'), 'first Upper');
        $this->assertEquals($expected, splitCamelCase('camelCase'), 'first Lower');
        $this->assertEquals(null, splitCamelcase(null), 'test null');
    }
}
