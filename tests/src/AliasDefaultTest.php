<?php

namespace PMVC;

class AliasDefaultTest extends TestCase
{
    public function pmvc_teardown()
    {
        unplug('fake');
    }

    public function testMultiDefaultAlias()
    {
        $obj = plug('fake', [_CLASS => __NAMESPACE__.'\FakeAlias']);
        $obj->setDefaultAlias([
            new FakeObject(),
            new FakeObjectB(),
        ]);
        $this->assertEquals(
            [
                'bbb',
                null,
            ],
            [
                $obj->b('bbb'),
                $obj->a(),
            ]
        );
        unplug($obj);
        $obj = plug('fake', [_CLASS => __NAMESPACE__.'\FakeAlias']);
        $obj->setDefaultAlias([
            new FakeObjectB(),
            new FakeObject(),
        ]);
        $this->assertEquals(
            [
                'bbb',
                'aaa--b',
            ],
            [
                $obj->b('bbb'),
                $obj->a('aaa'),
            ]
        );
    }

    /**
     * Test default alias method not exists.
     *
     * @expectedException Exception
     * @expectedExceptionMessage Method not found
     */
    public function testDefaultAliasNotFound()
    {
        $obj = plug('fake', [_CLASS => __NAMESPACE__.'\FakeAliasDefault']);
        $obj->setDefaultAlias([
            new FakeObject(),
        ]);
        $this->willThrow(
            function () use ($obj) {
                $obj->ggg();
            }
        );
    }
}
