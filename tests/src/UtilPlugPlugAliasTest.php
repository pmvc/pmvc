<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugPlugAliasTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        unplug('test');
        unplug('fakeAlias');
        addPlugInFolders(
            [], [
            'fakeAlias' => 'test',
            ]
        );
    }

    public function testAlias()
    {
        $class = __NAMESPACE__.'\FakePlugIn';
        plug(
            'test', [
            _CLASS => $class,
            ]
        );
        $abc = plug('fakeAlias');
        $this->assertEquals('test', $abc[NAME]);
        $this->assertEquals($class, $abc[_CLASS]);
    }

    /**
     * @expectedException DomainException
     */
    public function testPluginNotFound()
    {
        $abc = plug('fakeAlias');
    }
}
