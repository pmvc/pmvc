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
        $this->assertEquals(1, getOption('a'), 'Test for: '.get_class($a));
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
        $a->FakeTask();
        $this->assertEquals(1, getOption('d'));
        $this->assertEquals(0, getOption('e'));
    }
}
