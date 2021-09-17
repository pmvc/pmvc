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

    public function testIncludeMoreThanOnce()
    {
        l(__DIR__.'/../resources/empty.php');
        l(__DIR__.'/../resources/empty.php', null, ['once'=>false]);
        $this->assertTrue(true);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage File not found.
     */
    public function testIncludeNotExists()
    {
        $this->willThrow(
            function () {
                l(__DIR__.'/../resources/empty.php.fake');
            }
        );
    }

    public function testLoadEmpty()
    {
        $result = load(0);
        $this->assertEquals(1, $result);
    }

    public function testLoadWithImport()
    {
        $r = l(
            __DIR__.'/../resources/empty.php',
            'fakeTest',
            [
                'import' => ['fakeTest' => 'foo'],
            ]
        );
        $this->assertEquals('foo', $r->var['fakeTest']);
    }
}
