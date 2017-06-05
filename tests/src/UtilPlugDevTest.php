<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugDevTest extends PHPUnit_Framework_TestCase
{
    public function testDump()
    {
        d('test');
    }

    public function testLog()
    {
        log('test');
    }

    public function testIsDev()
    {
        isDev();
    }
}
