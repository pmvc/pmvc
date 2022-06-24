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
}

class FakeRun
{
    public static function foo($v)
    {
        return $v;
    }
}
