<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilFolderTest extends PHPUnit_Framework_TestCase
{
    public function testDeDuplicate()
    {
        addPlugInFolders(
            [
                __DIR__,
            ]
        );
        addPlugInFolders(
            [
                __DIR__,
            ]
        );
        $folders = folders(_PLUGIN);
        $arr = [];
        foreach ($folders['folders'] as $dir) {
            if (!isset($arr[$dir])) {
                $arr[$dir] = 0;
            }
            $arr[$dir]++;
        }
        $this->assertEquals(1, $arr[realpath(__DIR__)]);
    }

    public function testClean()
    {
        $folders = folders('fake', [], [], true);
        $expected = [
            'folders' => [],
            'alias'   => [],
        ];
        $this->assertEquals($expected, $folders);
    }
}
