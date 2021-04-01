<?php

namespace PMVC;

use Exception;

class AliasTest extends TestCase
{
    public function pmvc_setup() {
      unplug('fake');
      unplug('fakeChild');
    }

    public function getAliasProvider()
    {
        $parent = function() {return plug('fake', [_CLASS => __NAMESPACE__.'\FakeAlias']);};
        return [
            [$parent, 'data1'],
            [function() use ($parent) {$parent();return plug('fakeChild', [_CLASS => __NAMESPACE__.'\FakeAliasChild']);}, 'data2'],
            [function() { return new FakeAliasWithoutArrayAccess();}, 'data3'],
            [function() { return new FakeAliasWithoutArrayAccessChild();}, 'data4'],
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
        $obj->FakeTask();
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
        $obj->FakeTask();
        option('set', 'd', 0);
        option('set', 'e', 0);
        if (\PMVC\value($obj, ['parentAlias'])) {
            $this->assertTrue((bool) \PMVC\value($obj, ['parentAlias', 'faketask']), 'Test for: '.$tData);
        } elseif (\PMVC\value($obj, ['faketask'])) {
            $this->assertTrue((bool) \PMVC\value($obj, ['faketask']), 'Test for: '.$tData);
        } else {
            $plugin = plugInStore($obj[NAME]);
            $this->assertTrue((bool) \PMVC\value($plugin, ['parentAlias', 'faketask']), 'Test for: '.$tData);
        }
        $obj->FakeTask();
        $this->assertEquals(1, getOption('d'), 'Test for: '.$tData);
        $this->assertEquals(0, getOption('e'), 'Test for: '.$tData);
    }

    /**
     * Test parent method not exists.
     *
     * @expectedException \PMVC\PMVCUnitException
     */
    public function testParentMethodNotExists()
    {
        $this->expectException(PMVCUnitException::class);

        try {
            $child = plug('fakeChild', [_CLASS => __NAMESPACE__.'\FakeAliasChild']);
            $child->FakeNotExists();
        } catch (Exception $e) {
            throw new PMVCUnitException(
                $e->getMessage(),
                0
            );
        }
    }

    /**
     * Test caller with plugin.
     */
    public function testCallerWithPlugin()
    {
        $pFake = \PMVC\plug('fake', [_CLASS => __NAMESPACE__.'\FakeAlias']);
        $pFake->fakeTask();
        $this->assertTrue(
            is_a(
                $pFake['faketask']->caller,
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
        $oAlias->fakeTask();
        $oCaller = \PMVC\value($oAlias, ['faketask', 'caller']);
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
     * @expectedException        \PMVC\PMVCUnitException
     * @expectedExceptionMessage Method not found
     */
    public function testAliasObjectWithoutGetdir()
    {
        $this->expectException(PMVCUnitException::class);
        $this->expectExceptionMessage('Method not found');

        try {
            $oAlias = new FakeAliasWithOutGetDir();
            $result = $oAlias->faketask();
        } catch (Exception $e) {
            throw new PMVCUnitException(
                $e->getMessage(),
                0
            );
        }
    }

    /**
     * Test not defned class in alias file.
     *
     * @expectedException        \PMVC\PMVCUnitException
     * @expectedExceptionMessage Not defined default Class
     */
    public function testAliasFileWithoutClass()
    {
        $this->expectException(PMVCUnitException::class);
        $this->expectExceptionMessage('Not defined default Class');

        try {
            $oAlias = new FakeAliasWithoutArrayAccess();
            $oAlias->without_class();
        } catch (Exception $e) {
            throw new PMVCUnitException(
                $e->getMessage(),
                0
            );
        }
    }

    /**
     * Test defined class not exist.
     *
     * @expectedException        \PMVC\PMVCUnitException
     * @expectedExceptionMessage Default class not exists
     */
    public function testAliasFileWithWrongName()
    {
        $this->expectException(PMVCUnitException::class);
        $this->expectExceptionMessage('Default class not exists');

        try {
            $oAlias = new FakeAliasWithoutArrayAccess();
            $oAlias->with_wrong_name();
        } catch (Exception $e) {
            throw new PMVCUnitException(
                $e->getMessage(),
                0
            );
        }
    }

    /**
     * Test not implement invoke.
     *
     * @expectedException        \PMVC\PMVCUnitException
     * @expectedExceptionMessage Not implement __invoke
     */
    public function testAliasFileWithoutInvoke()
    {
        $this->expectException(PMVCUnitException::class);
        $this->expectExceptionMessage('Not implement __invoke');

        try {
            $oAlias = new FakeAliasWithoutArrayAccess();
            $oAlias->without_invoke();
        } catch (Exception $e) {
            throw new PMVCUnitException(
                $e->getMessage(),
                0
            );
        }
    }
}
