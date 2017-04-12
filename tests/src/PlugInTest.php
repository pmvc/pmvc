<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;
use SplObserver;
use SplSubject;

class PlugInTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        \PMVC\option('set', 'test', null);
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
        $actual = $plug->update(new fakeSplSubject());
        $expected = true;
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateMethodNotExists()
    {
        $subject = new fakeSplSubject();
        $subject->state = 'none';
        $class = __NAMESPACE__.'\FakePlug';
        $plug_name = 'fake_plug';
        $plug = plug(
            $plug_name, [
            _CLASS => $class,
            ]
        );
        $actual = $plug->update($subject);
        $this->assertEquals($plug[THIS], $actual);
    }

    public function testThis()
    {
        $a = [];
        $aThis = get($a, THIS, $a);
        $this->assertEquals($a, $aThis);
        $b = plug(
            'fake_plug', [
            _CLASS => __NAMESPACE__.'\FakePlug',
            ]
        );
        $bThis = get($b, THIS, $b);
        $this->assertEquals($b[THIS], $bThis);
    }

    public function testInstanceof()
    {
        $class = __NAMESPACE__.'\FakePlug';
        $plug_name = 'fake_plug';
        $plug = plug(
            $plug_name, [
            _CLASS => $class,
            ]
        );
        $this->assertTrue($plug->is($class));
    }
}

class fakeSplSubject implements SplSubject
{
    public $state = 'test';

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
        return $this->state;
    }
}
