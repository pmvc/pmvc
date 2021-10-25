<?php

namespace PMVC;

class UtilFolderTest extends TestCase
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

    public function testBasic()
    {
        $f1 = folders('fake', ['./']);
        $this->assertEquals(1, count($f1['folders']));
        $f2 = folders('fake', ['../']);
        $this->assertEquals(2, count($f2['folders']));
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
