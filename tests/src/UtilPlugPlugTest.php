<?php

namespace PMVC;

use OverflowException;

class UtilPlugPlugTest extends TestCase
{
    protected function pmvc_teardown()
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
        $class = __NAMESPACE__.'\FakePlugIn';
        $plug = 'test';
        $file = __DIR__.'/../resources/FakePlugFile.php';
        $test = plug($plug, [
            _PLUGIN_FILE => $file,
        ]);
        $this->assertEquals('1', $test['init'], 'call once for init');
        $this->assertEquals('2', $test['update'], 'call once for update');
        $this->assertEquals($plug, $test[NAME], 'check plugin name fail');
        $this->assertEquals(
            realpath($file),
            $test[_PLUGIN_FILE],
            'check plugin file fail'
        );
        $this->assertEquals(
            dirname(realpath($file)).'/',
            $test->getDir(),
            'test get dir'
        );
        plug($plug, ['new' => 1]);
        $this->assertEquals(
            '1',
            $test['init'],
            'should keep 1 for second call'
        );
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

    public function testCallPlugIn()
    {
        $test = callPlugIn('test');
        $this->assertFalse(!empty($test));
        plug('test', [
            _PLUGIN_FILE => __DIR__.'/../resources/FakePlugFile.php',
        ]);
        $test = callPlugIn('test');
        $this->assertTrue(!empty($test));
    }

    /**
     * @expectedException        \PMVC\PMVCUnitException
     * @expectedExceptionMessage Plug-in test not found
     */
    public function testPlugNotFound()
    {
        \PMVC\plug('unit')->throw(function () {
            plug('test');
        }, [$this, __FUNCTION__], $this);
    }

    /**
     * @expectedException        \PMVC\PMVCUnitException
     * @expectedExceptionMessage Plug name should be string.
     */
    public function testPlugNameNotString()
    {
        \PMVC\plug('unit')->throw(function () {
            plug(new \stdClass());
        }, [$this, __FUNCTION__], $this);
    }

    public function testCheckNullPlug()
    {
        $isExits = exists(null, 'plugin');
        $this->assertFalse($isExits);
    }

    /**
     * @expectedException        \PMVC\PMVCUnitException
     * @expectedExceptionMessage class not found
     */
    public function testPlugClassNotFound()
    {
        \PMVC\plug('unit')->throw(function () {
            plug('test', [
                _PLUGIN_FILE => __DIR__.'/../resources/FakePlugClassNotFound.php',
            ]);
        }, [$this, __FUNCTION__], $this);
    }

    /**
     * @expectedException        \PMVC\PMVCUnitException
     * @expectedExceptionMessage Class is not a plug-in(\PMVC\PlugIn) instance.
     */
    public function testPlugANonPlugin()
    {
        \PMVC\plug('unit')->throw(function () {
            plug('test', [
                _CLASS => __NAMESPACE__.'\NotPlugIn',
            ]);
        }, [$this, __FUNCTION__], $this);
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
        $dumpMock
            ->expects($this->atLeastOnce())
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
        $dumpMock
            ->expects($this->atLeastOnce())
            ->method('dump')
            ->with($this->anything(), 'plugin-folder');
        plug('debug', ['output' => $dumpMock])->setLevel('plugin-folder,debug');
        plug('dev')->onResetDebugLevel();
        $folders = addPlugInFolders(['fake'], []);
    }

    /**
     * @expectedException        \PMVC\PMVCUnitException
     * @expectedExceptionMessage PlugIn test: defined file not found. [foo]
     */
    public function testPlugNotExistsFile()
    {
        \PMVC\plug('unit')->throw(function () {
            plug('test', [
                _PLUGIN_FILE => 'foo',
            ]);
        }, [$this, __FUNCTION__], $this);
    }

    public function testLazyConfig()
    {
        $file = __DIR__.'/../resources/FakePlugFile.php';
        $unitKey = 'foo';
        $unitValue = 'bar';
        plug('test', [
            _PLUGIN_FILE => $file,
            _LAZY_CONFIG => function () use ($unitKey, $unitValue) {
                return [
                    $unitKey => $unitValue,
                ];
            },
        ]);
        $this->assertEquals($unitValue, plug('test')[$unitKey]);
    }

    public function testGetPlugs()
    {
        $file = __DIR__.'/../resources/FakePlugFile.php';
        plug('test', [
            _PLUGIN_FILE => $file,
        ]);
        $a = plugInStore();
        $this->assertTrue(in_array('test', $a));
    }

    public function testPlugSecurity()
    {
        $file = __DIR__.'/../resources/FakePlugFile.php';
        plug('testSecurity', [
            _PLUGIN_FILE => $file,
            _IS_SECURITY => true,
        ]);
        $this->assertTrue(exists('testSecurity', 'plugin'));
    }

    /**
     * @expectedException        \PMVC\PMVCUnitException
     * @expectedExceptionMessage You can not change security plugin
     */
    public function testUnPlugSecurityWarning()
    {
        $this->assertTrue(exists('testSecurity', 'plugin'));

        \PMVC\plug('unit')->throw(function () {
            unplug('testSecurity');
        }, [$this, __FUNCTION__], $this);
    }

    /**
     * @expectedException        \PMVC\PMVCUnitException
     * @expectedExceptionMessage Security plugin [testSecurity] already plug
     */
    public function testRePlugSecurityWarning()
    {
        \PMVC\plug('unit')->throw(function () {
            replug('testSecurity', new HashMap());
        }, [$this, __FUNCTION__], $this);
    }

    /**
     * @expectedException        OverflowException
     * @expectedExceptionMessage Security plugin [test] already plug
     */
    public function testSecurityPluginAlreadyExists()
    {
        $file = __DIR__.'/../resources/FakePlugFile.php';
        plug('test', [
            _PLUGIN_FILE => $file,
        ]);
        replug('test', [
            _PLUGIN_FILE => $file,
        ]);
        $this->assertTrue(exists('test', 'PlugIn'));
        \PMVC\plug('unit')->throw(function () use ($file) {
            replug('test', [
                _PLUGIN_FILE => $file,
                _IS_SECURITY => true,
            ]);
        }, [$this, __FUNCTION__], $this, false);
    }
}

class NotPlugIn
{
}
