<?php

namespace PMVC;

class UtilAssignTest extends TestCase
{
    public function testAssign()
    {
        $arr = ['a'=>'aa', 'b'=>'bb', 'c'=>'cc'];
        $actual = assign(['b'], $arr);
        $expected = [
            'b' => 'bb',
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testAssignWithNewKey()
    {
        $arr = ['a'=>'aa', 'b'=>'bb', 'c'=>'cc'];
        $actual = assign([['b', 'bb']], $arr);
        $expected = [
            'bb' => 'bb',
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testAssignWithDefaultValue()
    {
        $arr = ['a'=>'aa', 'c'=>'cc'];
        $actual = assign([['b', null, 'def']], $arr);
        $expected = [
            'b' => 'def',
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testAssignWithSeqArray()
    {
        $arr = ['foo', 'bar'];
        $actual = assign(['f', 'b'], $arr);
        $expected = [
            'f' => 'foo',
            'b' => 'bar',
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testAssignWithRest()
    {
        $arr = ['a'=>'aa', 'b'=>'bb', 'c'=>'cc'];
        $actual = assign(['a', 'b'], $arr, 'o');
        $expected = [
            'a' => 'aa',
            'b' => 'bb',
            'o' => [
                'c' => 'cc',
            ],
        ];
        $this->assertEquals($expected, $actual);
    }
}
