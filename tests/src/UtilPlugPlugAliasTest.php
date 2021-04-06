<?php

namespace PMVC;

use DomainException;

class UtilPlugPlugAliasTest extends TestCase
{
    protected function setup(): void
    {
        unplug('test');
        unplug('fakeAlias');
        addPlugInFolders(
            [],
            [
                'fakeAlias' => 'test',
            ]
        );
    }

    public function testAlias()
    {
        $class = __NAMESPACE__ . '\FakePlugIn';
        plug('test', [
            _CLASS => $class,
        ]);
        $abc = plug('fakeAlias');
        $this->assertEquals('test', $abc[NAME]);
        $this->assertEquals($class, $abc[_CLASS]);
    }

    /**
     * @expectedException DomainException
     */
    public function testPluginNotFound()
    {
        $this->willThrow(function () {
            $abc = plug('fakeAlias');
        }, false);
    }
}
