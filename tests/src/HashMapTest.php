<?php

namespace PMVC;

const SERIALIZE_81 = 'O:12:"PMVC\HashMap":1:{s:3:"foo";s:3:"bar";}';
const SERIALIZE_80 = 'C:12:"PMVC\HashMap":26:{a:1:{s:3:"foo";s:3:"bar";}}';

class HashMapTest extends TestCase
{
    public function testHashMap()
    {
        $hash = new HashMap();
        $this->assertEquals('PMVC\HashMap', get_class($hash));
    }

    public function testThis()
    {
        $hash = new FakeHash();
        $key = 'aaa';
        $value = 'bbb';
        $hash->mySet($key, $value);
        $this->assertEquals($value, $hash[$key]);
    }

    public function testRefObject()
    {
        $hash = new HashMap();
        $hash['abc'] = 123;
        $abc = $hash->abc;
        $abc_1 = &$abc();
        $new_value = 456;
        $abc_1 = $new_value;
        $this->assertEquals($new_value, $hash['abc']);
    }

    public function testRefObjectCall()
    {
        $hash = new HashMap();
        $hash->abc = 'def';
        $this->assertEquals(true, is_a($hash->abc, ns('BaseObject')));
        // can not call "$hash->abc();" directly,
        // else will get fatal error undefined method.
        // $hash->abc();
        $abc = $hash->abc;
        $this->assertEquals(true, is_a($abc, ns('BaseObject')));
        $this->assertEquals('def', ref($hash->abc));
        $this->assertEquals('def', $abc());
        $this->assertEquals('ghi', ref($hash->abc, 'ghi'));
        $abc('jkl');
        $this->assertEquals('jkl', $abc());
        $refAbc = &$abc();
        $this->assertEquals('jkl', $refAbc);
        $refAbc = 'mno';
        $this->assertEquals('mno', $abc());
        $this->assertEquals('mno', ref($hash->abc));
        $refAbc2 = &ref($hash->abc);
        $refAbc2 = 'pqr';
        $this->assertEquals('pqr', ref($hash->abc));
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage abc is not in hashmap
     */
    public function testGetObjectWithAttrNotExists()
    {
        $this->willThrow(
            function () {
                $hash = new HashMap();
                $tmp = $hash->abc;
            }
        );
    }

    public function testRefArray()
    {
        $hash = new HashMap();
        $hash['abc'] = 123;
        $abc = &$hash['abc'];
        $new_value = 789;
        $abc = $new_value;
        $this->assertEquals($new_value, $hash['abc']);
    }

    public function testPlug()
    {
        $class = __NAMESPACE__.'\FakePlugIn';
        $plug_name = 'fake_plug';
        $plug = plug(
            $plug_name,
            [
                _CLASS => $class,
            ]
        );
        $this->assertEquals($class, plug($plug_name)[_CLASS]);
    }

    public function testGetAll()
    {
        $arr = ['a' => '111', 'b' => '222'];
        $hash = new HashMap($arr);
        $this->assertEquals($arr, get($hash));
    }

    public function testKeyset()
    {
        $arr = ['a' => '111'];
        $hash = new HashMap($arr);
        $this->assertEquals(['a'], $hash->keySet());
    }

    public function testKeysetAsEmpty()
    {
        $hash = new FakeHash1();
        $this->assertEquals([], $hash->keySet());
    }

    public function testOffsetExists()
    {
        $arr = ['a' => '111'];
        $hash = new HashMap($arr);
        $this->assertTrue(isset($hash['a']));
        $this->assertFalse(isset($hash['b']));
    }

    public function testOffsetGet()
    {
        $arr = ['a' => '111'];
        $hash = new HashMap($arr);
        $this->assertEquals($arr['a'], $hash['a']);
        $a = $hash->a;
        $this->assertEquals($arr['a'], $a());
    }

    public function testOffsetSet()
    {
        $hash = new HashMap();
        $new_value = '111';
        $hash['a'] = $new_value;
        $newb_value = '222';
        $hash->b = $newb_value;
        $this->assertEquals($new_value, $hash['a']);
        $this->assertEquals($newb_value, $hash['b']);
    }

    public function testOffsetUnset()
    {
        $arr = ['a' => '111', 'b' => '222'];
        $hash = new HashMap($arr);
        $this->assertTrue(isset($hash['a']));
        unset($hash['a']);
        $this->assertFalse(isset($hash['a']));
        $this->assertTrue(isset($hash['b']));
        $hash->offsetUnset();
        $this->assertFalse(isset($hash['b']));
    }

    public function testUnsetObject()
    {
        $hash = new HashMap();
        $hash->a = 1;
        $this->assertTrue(isset($hash->a));
        unset($hash->a);
        $this->assertFalse(isset($hash->a));
    }

    public function testUnsetAll()
    {
        $hash = new HashMap();
        $hash['foo'] = 'bar';
        $this->assertTrue(1 === count($hash));
        unset($hash[null]);
        $this->assertTrue(\PMVC\isArrayAccess($hash));
        $hash['foo'] = 'bar';
        $this->assertTrue(1 === count($hash));
        unset($hash->null);
        $this->assertTrue(\PMVC\isArrayAccess($hash));
        unset($hash);
        $this->assertTrue(!isset($hash)); // become undefined
    }

    public function testAppend()
    {
        $arr = [
            'a' => ['aaa' => 111],
        ];
        $hash = new HashMap($arr);
        $newSet = [
            'a' => ['bbb' => 222],
        ];
        $hash[$newSet] = null;
        $this->assertEquals($newSet, get($hash));
        $hash[[]] = [
            'a' => ['ccc' => 333],
        ];
        $expected = $newSet;
        $expected['a']['ccc'] = 333;
        $this->assertEquals($expected, get($hash));
        $hash[] = ['ddd'];
        $this->assertEquals(['ddd'], $hash[0]);
    }

    public function testMergeHash()
    {
        $a = new HashMap(['a']);
        $a[[]] = new HashMap(['b', 'c']);
        $this->assertEquals(['a', 'b', 'c'], get($a));
    }

    /**
     * Key will merge if last key isnot same.
     *
     * @group mergeSameKey
     */
    public function testAppendSameKeyAndMerge()
    {
        $hash1 = new HashMap();
        $hash1[[]] = ['a'=>'111'];
        $hash1[[]] = ['a'=>'222'];
        $expected1 = [
            'a' => [
                '111',
                '222',
            ],
        ];
        $this->assertEquals($expected1, get($hash1));
    }

    /**
     * Key will not merge if last key is different.
     *
     * @group mergeSameKey
     */
    public function testAppendSameKeyAndNotMerge()
    {
        $hash2 = new HashMap();
        $hash2[[]] = ['a'=>['b1'=>'c1']];
        $hash2[[]] = ['a'=>['b2'=>'c2']];
        $expected2 = [
            'a' => [
                'b1' => 'c1',
                'b2' => 'c2',
            ],
        ];
        $this->assertEquals($expected2, get($hash2));
    }

    /**
     * Key will merge the last child key is same.
     *
     * @group mergeSameKey
     */
    public function testAppendSameKeyAndMergeChild()
    {
        $hash3 = new HashMap();
        $hash3[[]] = ['a'=>['b1'=>'c1']];
        $hash3[[]] = ['a'=>['b1'=>'c2']];
        $expected3 = [
            'a' => [
                'b1' => ['c1', 'c2'],
            ],
        ];
        $this->assertEquals($expected3, get($hash3));
    }

    /**
     * @expectedException Exception
     */
    public function testIllegalMerge()
    {
        $this->willThrow(function () {
            $hash = new HashMap();
            $hash[[]] = 'foo';
        });
    }

    public function testMergeDefault()
    {
        $hash = new HashMap(
            [
                'a' => 0,
            ]
        );
        $hash[
            [
                'a' => 1,
                'b' => 2,
            ]
            ] = [];
        $this->assertEquals(['a' => 0, 'b' => 2], get($hash));
    }

    /**
     * @group replace
     */
    public function testReplace()
    {
        $arr = [
            'aaa' => 111,
        ];
        $hash = new HashMap($arr);
        $hash[[]] = function () {
            return [
                'aaa' => 222,
            ];
        };
        $expected = [
            'aaa'=> '222',
        ];
        $this->assertEquals($expected, \PMVC\get($hash));
    }

    /**
     * @group replace
     */
    public function testReplaceSameKey()
    {
        $hash = new HashMap();
        $hash[[]] = ['a'=>['b1'=>'c1']];
        $hash[[]] = function () {
            return  ['a'=>['b1'=>'c2']];
        };

        $expected = [
            'a' => [
                'b1' => 'c2',
            ],
        ];
        $this->assertEquals($expected, \PMVC\get($hash));
    }

    public function testHashMapWalk()
    {
        $arr = ['foo' => ['a'], 'bar'];
        $map = new HashMap($arr, true);
        $expected = new HashMap(
            ['foo' => new HashMap(['a'], true), 'bar'],
            true
        );
        $this->assertEquals($expected, $map);
    }

    public function testHashMapToString()
    {
        $arr = ['foo' => 'bar'];
        $map = new HashMap($arr);
        $expected = version_compare(PHP_VERSION, '7.4.0') >= 0 ? SERIALIZE_81 : SERIALIZE_80;
        $this->assertEquals($expected, (string) $map);
    }

    public function testStringToHashMap()
    {
        $state = 'a:1:{s:3:"foo";s:3:"bar";}';
        $map = new HashMap($state);
        $expected = ['foo' => 'bar'];
        $this->assertEquals($expected, get($map));
    }

    public function testHashMapSerialize()
    {
        $arr = ['foo' => 'bar'];
        $map = new HashMap($arr);
        $str = serialize($map);
        $expected = version_compare(PHP_VERSION, '7.4.0') >= 0 ? SERIALIZE_81 : SERIALIZE_80;
        $this->assertEquals($expected, $str);
        $this->assertEquals(unserialize($str), $map);
    }

    public function testHashMapSerializeFunction()
    {
        $arr = ['foo' => 'bar'];
        $map = new HashMap($arr);
        $sSerialize = $map->serialize();
        $map->unserialize($sSerialize);
        $this->assertEquals($arr, get($map));
    }
}

class FakeHash extends HashMap
{
    public function mySet($k, $v)
    {
        $this[$k] = $v;
    }
}

class FakeHash1 extends HashMap
{
    public function __construct()
    {
    }
}
