<?php

namespace PMVC;

class UtilPlugTest extends \PHPUnit_Framework_TestCase
{
    public function testUnPlug()
    {
        $class = __NAMESPACE__.'\FakePlug';
        plug(
            'fake', [
            _CLASS => $class,
            ]
        );
        $this->assertTrue(exists('fake', 'PlugIn'));
        unPlug('fake');
        $this->assertFalse(exists('fake', 'PlugIn'));
    }

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

    /**
     * @function clean
     */
    public function testCleanKeepInArray()
    {
        $arr = [1, 2, 3];
        \PMVC\clean($arr);
        $expected = [];
        $this->assertEquals($expected, $arr);
    }
}
