<?php
namespace PMVC;
class UtilPlugTest extends \PHPUnit_Framework_TestCase
{
    function testUnPlug()
    {
        $class = __NAMESPACE__.'\FakePlug';
        plug('fake',array(
            _CLASS=>$class
        ));
        $this->assertTrue(exists('fake','PlugIn'));
        unPlug('fake');
        $this->assertFalse(exists('fake','PlugIn'));
    }

    function testSplitDir()
    {
        $dir='111:222';
        $expected=array('111','222');
        $actual=splitDir($dir);
        $this->assertEquals($expected, $actual);
        $winDir='aaa;bbb';
        $expected=array('aaa','bbb');
        $actual=splitDir($winDir);
        $this->assertEquals($expected, $actual);
    }
}

