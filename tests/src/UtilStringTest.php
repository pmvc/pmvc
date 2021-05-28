<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilStringTest extends PHPUnit_Framework_TestCase
{
    public function testSplitDir()
    {
        $dir = '111:222';
        $expected = ['111', '222'];
        $actual = splitDir($dir);
        $this->assertEquals($expected, $actual);
        $winDir = 'aaa;bbb';
        $expected = ['aaa', 'bbb'];
        $actual = splitDir($winDir);
        $this->assertEquals($expected, $actual);
    }

    public function testHasLastSlash()
    {
        $a = 'xxx/';
        $this->assertEquals($a, lastSlash($a));
    }

    public function testAppendLastSlash()
    {
        $a = 'xxx';
        $this->assertEquals($a.'/', lastSlash($a));
    }

    public function testCamelCase()
    {
        $expected = ['camel', 'case'];
        $this->assertEquals($expected, camelcase('CamelCase'), 'first Upper');
        $this->assertEquals($expected, camelcase('camelCase'), 'first Lower');
        $this->assertEquals(null, camelcase(null), 'test null');
    }

    public function testUtf8Export()
    {
        $s = utf8Export('str');
        $this->assertEquals('str', $s);
    }

    public function testUtf8ExportWhenUtf8PluginNotExists()
    {
        $fakeExists = function () {
            return false;
        };
        $s = utf8Export('str', $fakeExists);
        $this->assertEquals('str', $s);
        $o = utf8Export([], $fakeExists);
        $this->assertEquals([], $o);
    }

    public function testUtf8JsonEncode()
    {
        if (!defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            define('JSON_INVALID_UTF8_SUBSTITUTE', 0);
        }
        $s = utf8JsonEncode(['str', 'str2']);
        $this->assertEquals('["str","str2"]', $s);
    }

    public function testUtf8JsonEncodeWithObject()
    {
        $foo = new \StdClass();
        $foo->a = 'b';
        $foo->c = 'd';
        $acture = utf8JsonEncode($foo);
        $this->assertEquals('{"a":"b","c":"d"}', $acture);
    }

    public function testTpl()
    {
        $arr = [
            'foo1'=>'[BAR]1',
            'foo2'=>'[BAR]2'
        ];
        \PMVC\tpl($arr, ['BAR'], function(){
            return 'bar'; 
        });
        $this->assertEquals(['foo1'=>'bar1', 'foo2'=>'bar2'], $arr);
    }
}
