<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugTest extends PHPUnit_Framework_TestCase
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

    public function testPlugInCanNotPlug()
    {
        $plug = 'xxxxxxxxxxxxxxxxxx';
        $this->assertFalse(exists($plug, 'plug'));
    }

    /**
     * @expectedException DomainException
     * @expectedExceptionMessage Exists checker not support
     */
    public function testExistsNotSupport()
    {
        exists('test', 'xxx-type');
    }

    public function testExistsWithNull()
    {
        $this->assertFalse(exists(null, null));
    }

    public function testExistsWithZero()
    {
        $p = \PMVC\plug('0', [
            _CLASS=>'\PMVC\FakePlugIn'
        ]);
        $this->assertTrue(exists(0, 'plugin'));
        unplug('0');
    }
}
