<?php

namespace PMVC;

use Exception;

class AliasTest extends TestCase
{
    public function pmvc_setup()
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

    /**
     * Test not defned class in alias file.
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Not defined default Class
     */
    public function testAliasFileWithoutClass()
    {
        $this->willThrow(
            function () {
                $oAlias = new FakeAliasWithoutArrayAccess();
                $oAlias->without_class();
            }
        );
    }

    /**
     * Test defined class not exist.
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Default class not exists
     */
    public function testAliasFileWithWrongName()
    {
        $this->willThrow(
            function () {
                $oAlias = new FakeAliasWithoutArrayAccess();
                $oAlias->with_wrong_name();
            }
        );
    }

    /**
     * Test not implement invoke.
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Not implement __invoke
     */
    public function testAliasFileWithoutInvoke()
    {
        $this->willThrow(
            function () {
                $oAlias = new FakeAliasWithoutArrayAccess();
                $oAlias->without_invoke();
            }
        );
    }


    public function testPreCookFunctionName()
    {
        $oAlias = new FakeAliasWithoutArrayAccess();
        $fakeMethod = 'FOO';
        $oAlias->preCookFunctionName = function($m){
            return 'bar';
        };
        $oAlias->bar = function() {
          return 'bar';
        };
        $func = $oAlias->isCallable($fakeMethod);
        $this->assertTrue(empty($fund));
    }
}
