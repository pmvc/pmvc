<?php

namespace PMVC;

class UtilFileTest extends TestCase
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
