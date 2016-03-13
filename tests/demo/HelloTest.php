<?php

class HelloTest extends PHPUnit_Framework_TestCase
{
    public function testHello()
    {
        $test_str = 'Hello World!';
        $b = new PMVC\MappingBuilder();
        $b->addAction('index', function () use ($test_str) {
            return $test_str;
        });
        $mvc = \PMVC\getC();
        $result = $mvc($b);
        $this->assertEquals($test_str, $result[0]);
    }
}
