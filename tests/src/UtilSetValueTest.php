<?php

namespace PMVC;

class UtilSetValueTest extends TestCase
{
    public function testSetArray()
    {
        $expected = 'foo';
        $arr = [];
        $actual = value($arr, ['a', 'b', 'c', 'd'], null, $expected);
        $this->assertEquals(
            $expected,
            value($arr, ['a', 'b', 'c', 'd'])
        );
    }

    public function testSetObject()
    {
        $expected = 'bar';
        $arr = [
            'a' => [
                'b' => [
                    'c' => null,
                ],
            ],
        ];
        $arr = fromJson(json_encode($arr));
        $actual = value($arr, ['a', 'b', 'c'], null, $expected);
        $this->assertEquals(
            $expected,
            $arr->a->b->c
        );
    }

    public function testSetObjectWithAutoCreate()
    {
        $expected = 'bar';
        $arr = (object) [];
        $actual = value($arr, ['a', 'b', 'c', 'd'], function () {return (object) []; }, $expected);
        $this->assertEquals(
            $expected,
            $arr->a->b->c->d
        );
    }

    /**
     * Test pass by ref.
     */
    public function testPassByRef()
    {
        $actual = value(passByRef(['foo']), [0]);
        $this->assertEquals('foo', $actual);
    }
}
