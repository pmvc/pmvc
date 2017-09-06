<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugIncludeTest extends PHPUnit_Framework_TestCase
{
    private $_fakePlugFile;

    public function __construct()
    {
        $this->_fakePlugFile = __DIR__.'/../resources/FakePlugFile.php';
    }

    public function testIncludeMoreThanOnce()
    {
        l(__DIR__.'/../resources/empty.php');
        l(__DIR__.'/../resources/empty.php', null, false);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage File not found.
     */
    public function testIncludeNotExists()
    {
        l(__DIR__.'/../resources/empty.php.fake');
    }

    public function testPrependApp()
    {
        plug('controller', [
            _PLUGIN_FILE => $this->_fakePlugFile,
        ]);
        prependApp('fake', true, 'is_callable');
        unplug('controller');
    }

    public function testLoadEmpty()
    {
        $result = load(0);
        $this->assertEquals(1, $result);
    }
}

