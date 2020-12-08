<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilFileTest extends PHPUnit_Framework_TestCase
{
    public function testRealPathWithEmpty()
    {
        $false = realpath(null);
        $this->assertFalse($false);
    }

    public function testFindWithEmptyFolder()
    {
        $false = find('', [false]);
        $this->assertFalse($false);
    }
}
