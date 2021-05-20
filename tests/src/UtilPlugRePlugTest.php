<?php

namespace PMVC;

class UtilPlugRePlugTest extends TestCase
{
    protected function pmvc_setup()
    {
        unplug('test');
    }

    public function testRePlug()
    {
        $plug = 'test';
        $file = __DIR__.'/../resources/FakePlugFile.php';
        $this->assertTrue(!exists('test', 'plugin'));
        $test = plug(
            $plug,
            [
                _PLUGIN_FILE => $file,
            ]
        );
        $test['foo'] = 'bar';
        $this->assertEquals('bar', $test['foo']);
        replug('test', null, [
            _PLUGIN_FILE => $file,
        ]);
        $this->assertTrue(exists('test', 'plugin'));
        $this->assertEquals(null, $test['foo']);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Security plugin [testSecurity] already plug
     */
    public function testRePlugSecurityWarning()
    {
        $this->willThrow(
            function () {
                replug('testSecurity', new HashMap());
            }
        );
    }

    /**
     * @expectedException        DomainException 
     * @expectedExceptionMessage Security plugin [test] already plug
     */
    public function testSecurityPluginAlreadyExists()
    {
        $file = __DIR__.'/../resources/FakePlugFile.php';
        plug(
            'test',
            [
                _PLUGIN_FILE => $file,
            ]
        );
        replug(
            'test',
            [
                _PLUGIN_FILE => $file,
            ]
        );
        $this->assertTrue(exists('test', 'PlugIn'));
        $this->willThrow(
            function () use ($file) {
                replug(
                    'test',
                    [
                        _PLUGIN_FILE => $file,
                        _IS_SECURITY => true,
                    ]
                );
            },
            false
        );
    }
}
