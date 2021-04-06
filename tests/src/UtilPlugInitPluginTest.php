<?php

namespace PMVC;


class UtilPlugInitPluginTest extends TestCase
{
    protected function pmvc_setup()
    {
        unplug('test');
        addPlugInFolders(
            [
                __DIR__.'/../resources/plugin1',
            ]
        );
    }

    public function testIncludeOnly()
    {
        initPlugin(
            [
                'test' => [
                    _PLUGIN_FILE => __DIR__.'/../resources/FakePlugInclude.php',
                ],
            ],
            true
        );
        $this->assertTrue(class_exists(__NAMESPACE__.'\FakePlugInclude'), 'Class should exists');
        $this->assertFalse(exists('test', 'plugin'), 'Plugin should not exists');
    }

    public function testInitPlugWithoutConfig()
    {
        $plug = 'testplugin';
        unplug($plug);
        $plugins = initPlugin(
            [
                $plug => null,
            ]
        );
        $this->assertTrue(isset($plugins[$plug]));
    }

    public function testAlreadyPlug()
    {
        $plug = 'testplugin';
        $test = plug($plug);
        $plugins = initPlugin(
            [
                $plug => null,
            ]
        );
        $this->assertFalse(isset($plugins[$plug]));
    }

    public function testAlreadyPlugWithConfig()
    {
        $plug = 'testplugin';
        $test = plug($plug);
        $plugins = initPlugin(
            [
                $plug => [0],
            ]
        );
        $this->assertTrue(isset($plugins[$plug]));
    }
}
