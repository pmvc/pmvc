<?php
class HelloTest extends PHPUnit_Framework_TestCase
{
    function testHello()
    {
        $test_str='Hello World!';
        $b = new PMVC\MappingBuilder();
        $b->addAction('index', function () use ($test_str) {
            return $test_str;
        });
        $mvc = new PMVC\ActionController();
        $result = $mvc($b);
        $this->assertEquals($test_str, $result[0]);
    }
}

