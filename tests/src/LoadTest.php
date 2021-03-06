<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class LoadTest extends PHPUnit_Framework_TestCase
{
    public function testLoadWithLazyFunction()
    {
        \PMVC\Load::plug(
            function () {
                return [
                    [],
                    [],
                    [_VIEW_ENGINE => 'xxx'],
                ];
            }
        );
        $this->assertEquals('xxx', \PMVC\getOption(_VIEW_ENGINE));
        \PMVC\option('set', _VIEW_ENGINE, '');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testLoad()
    {
        \PMVC\Load::plug();
    }

    /**
     * @doesNotPerformAssertions
     * @runInSeparateProcess
     */
    public function testRunInSeparateProcess()
    {
        \PMVC\Load::plug(
            [
                'test' => [
                    _PLUGIN_FILE => __DIR__.'/../resources/FakePlugFile.php',
                ],
            ],
            ['./']
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetOption()
    {
        \PMVC\option('set', 'foo', 'bar');
        \PMVC\Load::plug([], [], ['foo' => 'bar']);
        $this->assertEquals('bar', \PMVC\getOption('foo'));
    }
}
