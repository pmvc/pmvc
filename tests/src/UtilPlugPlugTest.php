<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugPlugTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        unplug('test');
        unplug('debug');
        unplug('dev');
        unplug('test_test');
        unplug('testplugin');
        option('set', 'test', null);
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
        $this->assertEquals($plug, $test[NAME], 'check plugin name fail');
        $this->assertEquals(realpath($file), $test[_PLUGIN_FILE], 'check plugin file fail');
        $this->assertEquals(dirname(realpath($file)).'/', $test->getDir(), 'test get dir');
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

    public function testPlugFile()
    {
        plug('test', [
            _PLUGIN_FILE => __DIR__.'/../resources/FakePlugFile.php',
        ]);
        $this->assertEquals('test', plug('test')[NAME]);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Plug-in test not found
     */
    public function testPlugNotFound()
    {
        plug('test');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Plug name should be string.
     */
    public function testPlugNameNotString()
    {
        plug(new \stdClass());
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

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Class is not a plug-in(\PMVC\PlugIn) instance.
     */
    public function testPlugANonPlugin()
    {
        plug('test', [
            _CLASS => __NAMESPACE__.'\NotPlugIn',
        ]);
    }

    public function testGetConfigFromGlobalOption()
    {
        $test = plug('test', [
            _PLUGIN_FILE => __DIR__.'/../resources/FakePlugFile.php',
            'foo'        => 'ccc',
        ]);
        $this->assertEquals('ccc', $test['foo']);
        unplug('test');

        option('set', 'PLUGIN', ['test' => ['foo' => 'bar']]);
        plug('test', [
            _PLUGIN_FILE => __DIR__.'/../resources/FakePlugFile.php',
        ]);
        $this->assertEquals('bar', $test['foo']);
    }

    public function testGetConfigFromGlobalOptionWithUnderscore()
    {
        option('set', 'PLUGIN', ['test' => ['test' => ['a' => 'b']]]);
        $test = plug('test_test', [
            _PLUGIN_FILE => __DIR__.'/../resources/FakePlugFile.php',
        ]);
        $this->assertEquals('b', $test['a']);
    }

    public function testPluginDevInfo()
    {
        $dumpMock = $this->getMockBuilder(FakeDebugDump::class)
            ->setMethods(['dump'])
            ->getMock();
        $dumpMock->expects($this->atLeastOnce())
            ->method('dump')
            ->with($this->anything(), 'plug');

        plug('debug', ['output' => $dumpMock])->setLevel('plug,debug');
        plug('dev')->onResetDebugLevel();
        $test = plug('test', [
            _PLUGIN_FILE => __DIR__.'/../resources/FakePlugFile.php',
        ]);
        plug('asset', [
            _PLUGIN_FILE => __DIR__.'/../resources/FakePlugFile.php',
        ]);
    }

    public function testAddPluginFolderDevInfo()
    {
        $dumpMock = $this->getMockBuilder(FakeDebugDump::class)
            ->setMethods(['dump'])
            ->getMock();
        $dumpMock->expects($this->atLeastOnce())
            ->method('dump')
            ->with($this->anything(), 'plugin-folder');
        plug('debug', ['output' => $dumpMock])->setLevel('plugin-folder,debug');
        plug('dev')->onResetDebugLevel();
        $folders = addPlugInFolders(['fake'], []);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage PlugIn test: defined file not found. [foo]
     */
    public function testPlugNotExistsFile()
    {
        plug('test', [
            _PLUGIN_FILE => 'foo',
        ]);
    }

    public function testLazyConfig()
    {
        $file = __DIR__.'/../resources/FakePlugFile.php';
        $unitKey = 'foo';
        $unitValue = 'bar';
        plug('test', [
            _PLUGIN_FILE => $file,
            _LAZY_CONFIG => function() use ($unitKey, $unitValue){
                return [
                    $unitKey => $unitValue
                ];
            }
        ]);
        $this->assertEquals($unitValue, plug('test')[$unitKey]);
    }
}

class NotPlugIn
{
}
