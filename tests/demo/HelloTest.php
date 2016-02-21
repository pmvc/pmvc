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
        $mvc = new PMVC\ActionController();
        $result = $mvc($b);
        $this->assertEquals($test_str, $result[0]);
    }

    public function testPlugWithAction()
    {
        \PMVC\option('set', 'd', null);
        $mvc = new PMVC\ActionController();
        $mvc->store([
            _RUN_ACTION => 'FakeTask',
            _RUN_PARENT => __DIR__.'/../',
            _RUN_APP    => 'resources',
        ]);
        $mvc->plugApp();
        $this->assertEquals(1, \PMVC\getOption('d'));
    }
}
