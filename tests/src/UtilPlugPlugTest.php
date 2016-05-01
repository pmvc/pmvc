<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugPlugTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        unplug('test');
    }

    public function testAlias()
    {
        addPlugInFolder(null, [
            'abc' => 'test',
        ]);
        $class = __NAMESPACE__.'\FakePlug';
        plug(
            'test', [
            _CLASS => $class,
            ]
        );
        $abc = plug('abc');
        $this->assertEquals('test', $abc[_PLUGIN]);
        $this->assertEquals($class, $abc[_CLASS]);
    }

    public function testPlugFile()
    {
        plug('test', [
            _PLUGIN_FILE => __DIR__.'/../resources/FakePlugFile.php',
        ]);
        $this->assertEquals('test', plug('test')[_PLUGIN]);
    }

    public function testIncludeOnly()
    {
        initPlugin([
            'test' => [
                _PLUGIN_FILE => __DIR__.'/../resources/FakePlugInclude.php',
            ],
        ], true);
        $this->assertTrue(class_exists(__NAMESPACE__.'\FakePlugInclude'), 'Class should exists');
        $this->assertFalse(exists('test', 'plugin'), 'Plugin should not exists');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage plugin test not found 
     */
    public function testPlugNotFound()
    {
            plug('test'); 
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage [test/test.php] 
     */
    public function testPlugFileNotFound()
    {
            plug('test', [
                _PLUGIN_FILE=>__DIR__.'/../resources/FakePlugxxx.php'
            ] ); 
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage class not found 
     */
    public function testPlugClassNotFound()
    {
            plug('test', [
                _PLUGIN_FILE=>__DIR__.'/../resources/FakePlugClassNotFound.php'
            ] ); 
    }
}
