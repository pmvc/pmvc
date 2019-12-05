<?php

namespace PMVC;

use Exception;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_TestCase;

class UtilDevTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        unplug('debug');
        $this->_debugClass = __NAMESPACE__.
            '\FakeDebugPlugIn';
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testDump()
    {
        d('test');
    }

    public function testVariableDump()
    {
        $expected = [
          'test',
          [1=> '2', ''=>'1'],
          [0=> '0'],
        ];
        $i = 0;
        plug(
            'debug', [
            _CLASS      => $this->_debugClass,
            'dCallback' => function () use (&$i, $expected) {
                $args = func_get_args();
                $arr = fromJson($args[0], true);
                $this->assertEquals($expected[$i], $arr);
                $i++;
                if (is_array($arr) && 1 < count($arr)) {
                    $keys = array_keys($arr);
                    $int = true;
                    foreach ($keys as $k) {
                        if (!is_int($int)) {
                            $int = false;
                        }
                    }
                    $this->assertFalse($int);
                }
            },
            ]
        );
        v('test');
        v('1', '2');
        v(new HashMap(['0']));
    }

    public function testUtf8Dump()
    {
        $s = utf8Dump('str');
        $this->assertEquals("'str'", $s);
        $s = utf8Dump('str1', 'str2');
        $this->assertContains('array', $s);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage {"Error":"error","Debug":["debug-payload"]}
     */
    public function testTriggerJson()
    {
        try {
            triggerJson('error', ['debug-payload']);
        } catch (Exception $e) {
            throw new PHPUnit_Framework_Error(
                $e->getMessage(),
                0,
                $e->getFile(),
                $e->getLine()
            );
        }
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testLog()
    {
        log('test');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testIsDev()
    {
        isDev();
    }
}
