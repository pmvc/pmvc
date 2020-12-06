<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class AdapterTest extends PHPUnit_Framework_TestCase
{
    private $_class;
    private $_name;

    protected function setup(): void
    {
        $this->_class = __NAMESPACE__.'\FakePlugIn';
        $this->_name = 'fake_plug';
    }

    public function testUnplug()
    {
        $plug = plug(
            $this->_name,
            [
                _CLASS => $this->_class,
            ]
        );
        $this->assertTrue(exists($this->_name, 'plugin'));
        unplug($this->_name);
        $this->assertFalse(exists($this->_name, 'plugin'));
        $plug->onTest();
    }

    public function testToString()
    {
        $plug = plug(
            $this->_name,
            [
                _CLASS => $this->_class,
            ]
        );
        $actual = (string) $plug;
        $this->assertEquals($this->_class, $actual);
    }

    public function testUnset()
    {
        $plug = plug(
            $this->_name,
            [
                _CLASS => $this->_class,
            ]
        );
        $plug['a'] = 1;
        $this->assertTrue(isset($plug['a']));
        unset($plug['a']);
        $this->assertFalse(isset($plug['a']));
    }
}
