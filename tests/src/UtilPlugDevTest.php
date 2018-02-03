<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class UtilPlugDevTest extends PHPUnit_Framework_TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testDump()
    {
        d('test');
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
