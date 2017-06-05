<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugGetPlugsTest extends PHPUnit_Framework_TestCase
{
    public function testGetPlugs()
    {
        $result = getPlugs();
        $this->assertTrue(!empty($result));
    }
}
