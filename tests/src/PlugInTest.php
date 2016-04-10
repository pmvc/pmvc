<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;
use SplObserver;
use SplSubject;

class PlugInTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        getC()->store('test', null);
    }

    public function testUpdate()
    {
        $class = __NAMESPACE__.'\FakePlug';
        $plug_name = 'fake_plug';
        $plug = plug(
            $plug_name, [
            _CLASS => $class,
            ]
        );
        $plug->update(new fakeSplSubject());
        $this->assertEquals('ontest', getoption('test'));
    }
}

class fakeSplSubject implements SplSubject
{
    public function attach(SplObserver $SplObserver)
    {
    }

    public function detach(SplObserver $SplObserver)
    {
    }

    public function notify()
    {
    }

    public function getName()
    {
        return 'test';
    }
}
