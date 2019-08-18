<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class HashMapAllTest extends PHPUnit_Framework_TestCase
{
    public function testHashMapWalkWithSet()
    {
        $map = new HashMapAll([]);
        $map['foo'] = [ 'a', 'b' ];
        $expected = new HashMapAll([
          'foo' => new HashMapAll(['a', 'b']),
        ]);
        $this->assertEquals($expected, $map);
    }
}
