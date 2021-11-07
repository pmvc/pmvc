<?php

namespace PMVC;

use Exception;

class AliasFileTest extends TestCase
{
    public function getPlugIn($class) {
        return plug('fake', [_CLASS => __NAMESPACE__.'\\'.$class]);
    }

    public function pmvc_teardown()
    {
        unplug('fake');
    }

    public function testAliasFileFilter()
    {
        $p = $this->getPlugIn('FakeFileFilterAlias');
        $p->setFilter(true);
        $expected = 'FakeTask';
        $actual = $p->fakeTask($expected);
        $this->assertEquals($expected, $actual);
    }

    public function testAliasCustomFileFilter()
    {
        $p = $this->getPlugIn('FakeFileFilterAlias');
        $p->setFilter(function($name){
          $name = str_replace('_', '__', $name);
          return $name;
        });
        $expected = 'FakeTask';
        $actual = $p->fake__task($expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test not setup correct alias file filteer. 
     *
     * @expectedException        Exception
     * @expectedExceptionMessage aliasFileFilter 
     */
    public function testAliasCustomFileFilterNotSetCorreect()
    {
        $p = $this->getPlugIn('FakeFileFilterAlias');
        $p->setFilter(function(){ });
        $this->willThrow(
          function() use ($p){
          $p->foo();
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
}
