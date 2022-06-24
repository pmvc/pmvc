<?php

namespace PMVC;

class UtilRunTest extends TestCase
{
    public function testRun()
    {
        $actual = run([ns('FakeRun'), 'foo'], ['bar']);
        $this->assertEquals($actual, 'bar');
    }

    public function testNotCache()
    {
        $actual = run([ns('FakeRun'), 'foo'], ['bar1'], function ($v) {
            return false;
        });
        $this->assertEquals($actual, null);
    }

    public function testChangeResult()
    {
        $actual1 = run([ns('FakeRun'), 'foo'], ['bar2'], function (&$v) {
            $v = 'barbar';

            return true;
        });
        $this->assertEquals($actual1, 'barbar');
        $actual2 = run([ns('FakeRun'), 'foo'], ['bar2']);
        $this->assertEquals($actual2, 'barbar');
    }
}

class FakeRun
{
    public static function foo($v)
    {
        return $v;
    }
}
