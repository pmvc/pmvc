<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugPlugTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        unplug('test');
        unplug('testplugin');
    }

    public function testPlug()
    {
        $class = __NAMESPACE__.'\FakePlug';
        $plug = 'test';
        $file = __DIR__.'/../resources/FakePlugFile.php';
        $test = plug($plug, [
            _PLUGIN_FILE => $file,
        ]);
        $this->assertEquals('1', $test['init'], 'call once for init');
        $this->assertEquals('2', $test['update'], 'call once for update');
        $this->assertEquals($plug, $test[_PLUGIN], 'check plugin name fail');
        $this->assertEquals($file, $test[_PLUGIN_FILE], 'check plugin file fail');
        $this->assertEquals(dirname($file).'/', $test->getDir(), 'test get dir');
        plug($plug, ['new' => 1]);
        $this->assertEquals('1', $test['init'], 'should keep 1 for second call');
        $this->assertEquals('3', $test['update'], 'call twice for udate');
        $this->assertEquals('1', $test['new'], 'assign new value');
    }

    public function testPlugWithOneFolder()
    {
        addPlugInFolders([__DIR__.'/../resources/plugin1']);
        $test = plug('testplugin');
        $this->assertEquals('plugin1', $test['test']);
    }

    public function testPlugWithFolders()
    {
        addPlugInFolders([
            __DIR__.'/../resources/plugin1',
            __DIR__.'/../resources/plugin2',
        ]);
        $test = plug('testplugin');
        $this->assertEquals('plugin2', $test['test']);
    }

    public function testAlias()
    {
        addPlugInFolders([], [
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
                _PLUGIN_FILE => __DIR__.'/../resources/FakePlugxxx.php',
            ]);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage class not found
     */
    public function testPlugClassNotFound()
    {
        plug('test', [
                _PLUGIN_FILE => __DIR__.'/../resources/FakePlugClassNotFound.php',
            ]);
    }
}