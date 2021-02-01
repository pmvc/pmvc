<?php

namespace PMVC;

use Exception;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_TestCase;

class AliasTest extends PHPUnit_Framework_TestCase
{
    public function getAliasProvider()
    {
        return [
            [plug('fake', [_CLASS => __NAMESPACE__.'\FakeAlias'])],
            [plug('fakeChild', [_CLASS => __NAMESPACE__.'\FakeAliasChild'])],
            [new FakeAliasWithoutArrayAccess()],
            [new FakeAliasWithoutArrayAccessChild()],
        ];
    }

    /**
     * @dataProvider getAliasProvider
     */
    public function testDefaultAlias($a)
    {
        option('set', 'a', 0);
        $a->a();
        $name = get_class($a);
        if (\PMVC\value($a, [NAME])) {
            $name = $a[NAME];
        }
        $this->assertEquals(1, getOption('a'), 'Test for: '.$name);
    }

    /**
     * @dataProvider getAliasProvider
     */
    public function testConfigAlias($a)
    {
        if (isArray($a)) {
            $a['c'] = new FakeInvoke();
        } else {
            $a->c = new FakeInvoke();
        }
        option('set', 'c', 0);
        $a->c();
        $this->assertEquals(1, getOption('c'), 'Test for: '.get_class($a));
    }

    /**
     * @dataProvider getAliasProvider
     */
    public function testSourceFromFile($a)
    {
        option('set', 'd', 0);
        $a->FakeTask();
        $this->assertEquals(1, getOption('d'));
    }

    /**
     * Test file alias will cache to attribute alias.
     *
     * @dataProvider getAliasProvider
     */
    public function testFileAliasCache($a)
    {
        $a->FakeTask();
        option('set', 'd', 0);
        option('set', 'e', 0);
        if (\PMVC\value($a, ['parentAlias'])) {
            $this->assertTrue((bool) \PMVC\value($a, ['parentAlias', 'faketask']));
        } elseif (\PMVC\value($a, ['faketask'])) {
            $this->assertTrue((bool) \PMVC\value($a, ['faketask']));
        } else {
            $plugin = plugInStore($a[NAME]);
            $this->assertTrue((bool) \PMVC\value($plugin, ['parentAlias', 'faketask']));
        }
        $a->FakeTask();
        $this->assertEquals(1, getOption('d'));
        $this->assertEquals(0, getOption('e'));
    }

    /**
     * Test parent method not exists.
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testParentMethodNotExists()
    {
        try {
            $child = plug('fakeChild', [_CLASS => __NAMESPACE__.'\FakeAliasChild']);
            $child->FakeNotExists();
        } catch (Exception $e) {
            throw new PHPUnit_Framework_Error(
                $e->getMessage(),
                0,
                $e->getFile(),
                $e->getLine()
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
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage Method not found
     */
    public function testAliasObjectWithoutGetdir()
    {
        try {
            $oAlias = new FakeAliasWithOutGetDir();
            $result = $oAlias->faketask();
        } catch (Exception $e) {
            throw new PHPUnit_Framework_Error(
                $e->getMessage(),
                0,
                $e->getFile(),
                $e->getLine()
            );
        }
    }

    /**
     * Test not defned class in alias file.
     *
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage Not defined default Class
     */
    public function testAliasFileWithoutClass()
    {
        try {
            $oAlias = new FakeAliasWithoutArrayAccess();
            $oAlias->without_class();
        } catch (Exception $e) {
            throw new PHPUnit_Framework_Error(
                $e->getMessage(),
                0,
                $e->getFile(),
                $e->getLine()
            );
        }
    }

    /**
     * Test defined class not exist.
     *
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage Default class not exists
     */
    public function testAliasFileWithWrongName()
    {
        try {
            $oAlias = new FakeAliasWithoutArrayAccess();
            $oAlias->with_wrong_name();
        } catch (Exception $e) {
            throw new PHPUnit_Framework_Error(
                $e->getMessage(),
                0,
                $e->getFile(),
                $e->getLine()
            );
        }
    }

    /**
     * Test not implement invoke.
     *
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage Not implement __invoke
     */
    public function testAliasFileWithoutInvoke()
    {
        try {
            $oAlias = new FakeAliasWithoutArrayAccess();
            $oAlias->without_invoke();
        } catch (Exception $e) {
            throw new PHPUnit_Framework_Error(
                $e->getMessage(),
                0,
                $e->getFile(),
                $e->getLine()
            );
        }
    }
}