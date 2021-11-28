<?php

namespace PMVC;

class UtilStringTplTest extends TestCase
{
    public function testTpl()
    {
        $arr = [
            'foo1'=> 'BAR1',
            'foo2'=> '[BAR]2',
        ];
        $noReplace = \PMVC\tpl($arr['foo1'], ['BAR'], function () {
            return 'bar';
        });
        $haveReplace = \PMVC\tpl($arr['foo2'], ['BAR'], function () {
            return 'bar';
        });
        $this->assertEquals('BAR1', $noReplace);
        $this->assertEquals('bar2', $haveReplace);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage \\PMVC\\tpl
     */
    public function testTplNotReturnString()
    {
        $this->willThrow(function () {
            \PMVC\tpl('[foo]', ['foo'], function () {return null; });
        });
    }

    public function testTplArrayReplace()
    {
        $tpl = 'aaa[FOO]_[BAR]bbb';
        $data = ['FOO' => 'foo1', 'BAR' => 'bar1'];
        $actual1 = tplArrayReplace($tpl, $data);
        $this->assertEquals('aaafoo1_bar1bbb', $actual1);
        $actual2 = tplArrayReplace($tpl, ['BAR'], $data);
        $this->assertEquals('aaa[FOO]_bar1bbb', $actual2);
    }

    public function testTplArrayReplaceKeyNotExists()
    {
        $data = [];
        $tpl = 'aaa[FOO]_[BAR]bbb';
        $actual = tplArrayReplace($tpl, ['BAR'], $data);
        $this->assertEquals('aaa[FOO]_bbb', $actual);
    }
}
