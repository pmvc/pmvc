<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilTest extends PHPUnit_Framework_TestCase
{
    public function testPlugInCanNotPlug()
    {
        $plug = 'xxxxxxxxxxxxxxxxxx';
        $this->assertFalse(exists($plug, 'plug'));
    }

    /**
     * @expectedException        DomainException
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

    public function testIsOkToPlugWithAlreadyPlug()
    {
        $p = \PMVC\plug(
            'test',
            [
                _CLASS=> '\PMVC\FakePlugIn',
            ]
        );
        $this->assertTrue(exists('test', 'plug'));
    }

    public function testExistsWithZero()
    {
        $p = \PMVC\plug(
            '0',
            [
                _CLASS=> '\PMVC\FakePlugIn',
            ]
        );
        $this->assertTrue(exists(0, 'plugin'));
        unplug('0');
    }
}
