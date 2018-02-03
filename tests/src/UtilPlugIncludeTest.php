<?php

namespace PMVC;

use Exception;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_TestCase;

class UtilPlugIncludeTest extends PHPUnit_Framework_TestCase
{
    private $_fakePlugFile;

    public function __construct()
    {
        parent::__construct();
        $this->_fakePlugFile = __DIR__.'/../resources/FakePlugFile.php';
    }

    /**
     * @doesNotPerformAssertions
     */
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
        try {
            l(__DIR__.'/../resources/empty.php.fake');
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
     * @doesNotPerformAssertions
     */
    public function testPrependApp()
    {
        plug('controller', [
            _PLUGIN_FILE => $this->_fakePlugFile,
        ]);
        prependApp('fake', true, 'isdev');
        unplug('controller');
    }

    public function testLoadEmpty()
    {
        $result = load(0);
        $this->assertEquals(1, $result);
    }
}
