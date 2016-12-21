<?php

namespace PMVC;

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
    public function testSourceFileAlias($a)
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
            $obj = getOption(PLUGIN_INSTANCE);
            $plugin = $obj[$a[NAME]];
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
        $child = plug('fakeChild', [_CLASS => __NAMESPACE__.'\FakeAliasChild']);
        $child->FakeNotExists();
    }

    /**
     * Test caller with plugin.
     */
     public function testCallerWithPlugin()
     {
         $pFake = \PMVC\plug('fake', [_CLASS => __NAMESPACE__.'\FakeAlias']);
         $pFake->faketask();
         $this->assertTrue(is_a(
            $pFake['faketask']->caller,
            '\PMVC\Adapter'
        ));
     }

    /**
     * Test caller without plugin.
     */
     public function testCallerWithoutPlugin()
     {
         $oAlias = new FakeAliasWithoutArrayAccess();
         $oAlias->faketask();
         $obj = \PMVC\value($oAlias, ['faketask', 'caller']);
         $this->assertTrue(!is_a(
                $obj,
                '\PMVC\Adapter'
            ) &&
            is_object($obj)
        );
     }
}
