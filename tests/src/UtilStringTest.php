<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilStringTest extends PHPUnit_Framework_TestCase
{
    public function testSplitDir()
    {
        $dir = '111:222';
        $expected = ['111', '222'];
        $actual = splitDir($dir);
        $this->assertEquals($expected, $actual);
        $winDir = 'aaa;bbb';
        $expected = ['aaa', 'bbb'];
        $actual = splitDir($winDir);
        $this->assertEquals($expected, $actual);
    }

    public function testHasLastSlash()
    {
        $a = 'xxx/';
        $this->assertEquals($a, lastSlash($a));
    }

    public function testAppendLastSlash()
    {
        $a = 'xxx';
        $this->assertEquals($a.'/', lastSlash($a));
    }

    public function testCamelCase()
    {
        $expected = ['camel', 'case'];
        $this->assertEquals($expected, camelcase('CamelCase'), 'first Upper');
        $this->assertEquals($expected, camelcase('camelCase'), 'first Lower');
    }
}
