<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class LoadTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        \PMVC\Load::plug();
    }

    /**
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
        \PMVC\Load::plug([], [], ['foo'=>'bar']);
        $this->assertEquals('bar', \PMVC\getOption('foo'));
    }
}
