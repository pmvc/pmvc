<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugUnPlugTest extends PHPUnit_Framework_TestCase
{
    public function testUnplugNotExist()
    {
        $result = unplug('xxx');
        $this->assertFalse($result);
    }

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
}
