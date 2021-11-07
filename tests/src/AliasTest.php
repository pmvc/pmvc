<?php

namespace PMVC;

use Exception;

class AliasTest extends TestCase
{
    public function pmvc_teardown()
    {
        unplug('fake');
        unplug('fakeChild');
    }

    public function getAliasProvider()
    {
        $parent = function () {
            return plug('fake', [_CLASS => __NAMESPACE__.'\FakeAlias']);
        };

        return [
            [$parent, 'data1'],
            [function () use ($parent) {
                $parent();

                return plug('fakeChild', [_CLASS => __NAMESPACE__.'\FakeAliasChild']);
            }, 'data2'],
            [function () {
                return new FakeAliasWithoutArrayAccess();
            }, 'data3'],
            [function () {
                return new FakeAliasWithoutArrayAccessChild();
            }, 'data4'],
        ];
    }

    /**
     * @dataProvider getAliasProvider
     */
    public function testDefaultAlias($a, $tData)
    {
        option('set', 'a', 0);
        $obj = $a();
        $obj->a();
        $this->assertEquals(1, getOption('a'), 'Test for: '.$tData);
    }

    /**
     * @dataProvider getAliasProvider
     */
    public function testConfigAlias($a, $tData)
    {
        $obj = $a();
        if (isArray($obj)) {
            $obj['c'] = new FakeInvoke();
        } else {
            $obj->c = new FakeInvoke();
        }
        option('set', 'c', 0);
        $obj->c();
        $this->assertEquals(1, getOption('c'), 'Test for: '.$tData);
    }

    /**
     * @dataProvider getAliasProvider
     */
    public function testSourceFromFile($a, $tData)
    {
        $obj = $a();
        option('set', 'd', 0);
        $obj->fake_task();
        $this->assertEquals(1, getOption('d'), 'Test for: '.$tData);
    }

    /**
     * Test file alias will cache to attribute alias.
     *
     * @dataProvider getAliasProvider
     */
    public function testFileAliasCache($a, $tData)
    {
        $obj = $a();
        $obj->fake_task();
        option('set', 'd', 0);
        option('set', 'e', 0);
        if (\PMVC\value($obj, ['parentAlias'])) {
            $this->assertTrue((bool) \PMVC\value($obj, ['parentAlias', 'fake_task']), 'Test for: '.$tData);
        } elseif (\PMVC\value($obj, ['fake_task'])) {
            $this->assertTrue((bool) \PMVC\value($obj, ['fake_task']), 'Test for: '.$tData);
        } else {
            $plugin = plug($obj[NAME]);
            $this->assertTrue((bool) \PMVC\value(passByRef($plugin->getParentAlias()), ['fake_task']), 'Test for: '.$tData);
        }
        $obj->fake_task();
        $this->assertEquals(1, getOption('d'), 'Test for: '.$tData);
        $this->assertEquals(0, getOption('e'), 'Test for: '.$tData);
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
     * Test parent method not exists.
     *
     * @expectedException Exception
     */
    public function testParentMethodNotExists()
    {
        $this->willThrow(
            function () {
                $child = plug('fakeChild', [_CLASS => __NAMESPACE__.'\FakeAliasChild']);
                $child->FakeNotExists();
            }
        );
    }

    /**
     * Test caller with plugin.
     */
    public function testCallerWithPlugin()
    {
        $pFake = \PMVC\plug('fake', [_CLASS => __NAMESPACE__.'\FakeAlias']);
        $pFake->fake_task();
        $this->assertTrue(
            is_a(
                $pFake['fake_task']->caller,
                '\PMVC\Adapter'
            )
        );
    }

    /**
     * Test caller without plugin.
     */
    public function testCallerWithoutPlugin()
    {
        $oAlias = new FakeAliasWithoutArrayAccess();
        $oAlias->fake_task();
        $oCaller = \PMVC\value($oAlias, ['fake_task', 'caller']);
        $this->assertTrue(
            !is_a(
                $oCaller,
                '\PMVC\Adapter'
            ) &&
            is_object($oCaller)
        );
    }

    /**
     * Test alias without implemnet getdir.
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Method not found
     */
    public function testAliasObjectWithoutGetdir()
    {
        $this->willThrow(
            function () {
                $oAlias = new FakeAliasWithOutGetDir();
                $result = $oAlias->faketask();
            }
        );
    }

    public function testPreCookFunctionName()
    {
        $oAlias = new FakeAliasWithoutArrayAccess();
        $fakeMethod = 'FOO';
        $oAlias->preCookFunctionName = function ($m) {
            return 'bar';
        };
        $oAlias->bar = function () {
            return 'bar';
        };
        $func = $oAlias->isCallable($fakeMethod);
        $this->assertTrue(empty($fund));
    }
}
