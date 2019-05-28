<?php

namespace PMVC;

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
        plug(
            'debug', [
            _CLASS      => $this->_debugClass,
            'dCallback' => function () {
                $args = func_get_args();
                $arr = fromJson($args[0], true);
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
        $error = triggerJson('error', ['debug-payload']);
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
