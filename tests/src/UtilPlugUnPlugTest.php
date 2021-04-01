<?php

namespace PMVC;

class UtilPlugUnPlugTest extends TestCase
{
    public function testUnplugNotExist()
    {
        $result = unplug('xxx');
        $this->assertFalse($result);
    }

    public function testUnPlug()
    {
        $class = __NAMESPACE__.'\FakePlugIn';
        plug(
            'fake',
            [
                _CLASS => $class,
            ]
        );
        $this->assertTrue(exists('fake', 'PlugIn'));
        unPlug('fake');
        $this->assertFalse(exists('fake', 'PlugIn'));
    }
}
