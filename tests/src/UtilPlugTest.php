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
}

