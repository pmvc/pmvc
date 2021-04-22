<?php

namespace PMVC;

class UtilDevTest extends TestCase
{
    protected function pmvc_setup()
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
            new HashMap(['0']),
        ];
        $i = 0;
        plug(
            'debug',
            [
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

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage {"Error":"error","Debug":["debug-payload"]}
     */
    public function testTriggerJson()
    {
        $this->willThrow(function () {
            triggerJson('error', ['debug-payload']);
        });
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
