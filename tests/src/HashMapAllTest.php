<?php

namespace PMVC;

class HashMapAllTest extends TestCase
{
    public function testHashMapWalkWithSet()
    {
        $map = new HashMapAll([]);
        $map['foo'] = [ 'a', 'b' ];
        $expected = new HashMapAll(
            [
                'foo' => new HashMapAll(['a', 'b']),
            ]
        );
        $this->assertEquals($expected, $map);
    }

    public function testConstruct()
    {
        $arr = ['foo'=>['a'], 'bar'];
        $map = new FakeHashAll($arr);
        $expected = new FakeHashAll(['foo'=>new FakeHashAll(['a'], true), 'bar'], true);
        $this->assertEquals($expected, $map);
    }
}

class FakeHashAll extends HashMapAll
{
}
