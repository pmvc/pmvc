<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class AliasTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultAlias()
    {
        $a = plug('fake', [_CLASS => __NAMESPACE__.'\FakeAlias']);
        $a->a();
        $this->assertEquals(1, getOption('a'));
    }

    public function testConfigAlias()
    {
        $a = plug('fake', [_CLASS => __NAMESPACE__.'\FakeAlias']);
        $a['c'] = new FakeInvoke();
        $a->c();
        $this->assertEquals(1, getOption('c'));
    }

    public function testSourceFileAlias()
    {
        $a = plug('fake', [_CLASS => __NAMESPACE__.'\FakeAlias']);
        $a->FakeTask();
        $this->assertEquals(1, getOption('d'));
    }
}
