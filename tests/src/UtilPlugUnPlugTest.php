<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugUnPlugTest extends PHPUnit_Framework_TestCase
{
    public function testUnplugNotExist()
    {
        $result = unplug('xxx');
        $this->assertFalse($result);
    }
}
