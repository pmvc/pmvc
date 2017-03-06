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
    public function testrunInSeparateProcess()
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
}
