<?php

namespace PMVC;

use Exception;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_TestCase;

class UtilIncludeTest extends PHPUnit_Framework_TestCase
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
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage File not found.
     */
    public function testIncludeNotExists()
    {
        $this->expectException(PHPUnit_Framework_Error::class);
        $this->expectExceptionMessage("File not found.");
        try {
            l(__DIR__.'/../resources/empty.php.fake');
        } catch (Exception $e) {
            throw new PHPUnit_Framework_Error(
                $e->getMessage(),
                0,
            );
        }
    }

    public function testLoadEmpty()
    {
        $result = load(0);
        $this->assertEquals(1, $result);
    }
}
