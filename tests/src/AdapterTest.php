<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class AdapterTest extends PHPUnit_Framework_TestCase
{
    private $_class;
    private $_name;

    public function setup()
    {
        $this->_class = __NAMESPACE__.'\FakePlug';
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
}
