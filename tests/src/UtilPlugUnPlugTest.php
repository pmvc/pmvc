<?php

namespace PMVC;

use Exception;

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
     * @expectedException \PMVC\PMVCUnitException
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
        $this->expectException(PMVCUnitException::class);
        $this->expectExceptionMessage('You can not change security plugin');

        try {
            plug('fake-can-not-replug', [_CLASS => $class]);
        } catch (Exception $e) {
            throw new PMVCUnitException(
                $e->getMessage(),
                0
            );
        }
    }
}
