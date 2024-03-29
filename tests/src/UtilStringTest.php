<?php

namespace PMVC;

class UtilStringTest extends TestCase
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

    public function testLastSlashWithEmpty()
    {
        $a = '';
        $this->assertEquals('/', lastSlash($a));
    }

    public function testAppendLastSlash()
    {
        $a = 'xxx';
        $this->assertEquals($a.'/', lastSlash($a));
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
        $foo = new \stdClass();
        $foo->a = 'b';
        $foo->c = 'd';
        $acture = utf8JsonEncode($foo);
        $this->assertEquals('{"a":"b","c":"d"}', $acture);
    }
}
