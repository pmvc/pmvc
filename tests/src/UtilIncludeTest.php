<?php

namespace PMVC;

class UtilIncludeTest extends TestCase
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
     * @expectedException        Exception
     * @expectedExceptionMessage File not found.
     */
    public function testIncludeNotExists()
    {
        $this->willThrow(function () {
            l(__DIR__.'/../resources/empty.php.fake');
        });
    }

    public function testLoadEmpty()
    {
        $result = load(0);
        $this->assertEquals(1, $result);
    }
}
