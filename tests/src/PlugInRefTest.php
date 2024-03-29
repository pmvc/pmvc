<?php

namespace PMVC;

class PluginRefTest extends TestCase
{
    private $_name = 'fake_plug';

    protected function pmvc_setup()
    {
        unplug($this->_name);
        $class = __NAMESPACE__.'\FakePlugIn';
        $plug = plug(
            $this->_name,
            [
                _CLASS => $class,
            ]
        );
    }

    public function testRef()
    {
        $p = \PMVC\plug($this->_name);
        $p['foo'] = 'bar';
        $abc = &$p['foo'];
        $this->assertEquals('bar', $abc);
        $abc = 'def';
        $this->assertEquals('def', $p['foo']);
    }

    public function testStringWithoutRef()
    {
        $p = \PMVC\plug($this->_name);
        $p['foo'] = 'bar';
        $abc = $p['foo'];
        $this->assertEquals('bar', $abc);
        $abc = 'def';
        $this->assertEquals('bar', $p['foo']);
    }

    public function testNonStringWithoutRef()
    {
        $p = \PMVC\plug($this->_name);
        $key = true;
        $p[$key] = 'bar';
        // non string will always not connect ref
        $abc = &passByRef($p[$key]);
        $this->assertEquals('bar', $abc);
        $abc = 'def';
        $this->assertEquals('bar', $p[$key]);
    }

    public function testGetAll()
    {
        $a = [1, 2, 3];
        $p = \PMVC\plug($this->_name);
        set($p, [1, 2, 3]);
        $all = get($p);
        unset($all[THIS]);
        $this->assertTrue(empty(array_diff($a, $all)));
    }
}
