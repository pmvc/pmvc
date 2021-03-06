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

    /**
     * Test can not unplug reject plug.
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Security plugin [fake-can-not-replug] already plug or unplug
     */
    public function testRejectPlug()
    {
        $class = __NAMESPACE__.'\FakePlugIn';
        plug(
            'fake-can-not-replug',
            [
                _CLASS => $class,
            ]
        );
        $this->assertTrue(exists('fake-can-not-replug', 'PlugIn'));
        unPlug('fake-can-not-replug', true);

        $this->willThrow(
            function () use ($class) {
                plug('fake-can-not-replug', [_CLASS => $class]);
            }
        );
    }
}
