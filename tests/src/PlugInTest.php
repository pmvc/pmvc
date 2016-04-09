<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;
use SplSubject;
use SplObserver;

class PlugInTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        getC()->store('test',null);
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
    function attach(SplObserver $SplObserver) {}
    function detach(SplObserver $SplObserver) {}
    function notify() {}
    function getName() {return 'test';}
}
