<?php

namespace PMVC;

class ImportClassTest extends TestCase
{
    public function testImportClassWithFilePath()
    {
        $class = importClass(__DIR__.'/../resources/FakePlugFile');
        $this->assertEquals('PMVC\FakePlugFile', $class);
    }
}
