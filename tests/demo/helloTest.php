<?php
class HelloTest extends PHPUnit_Framework_TestCase
{
    function testHello(){
        $test_str='Hello World!';
        $b = new PMVC\MappingBuilder();
        $b->addAction('index', array(
            _FUNCTION=>function() use ($test_str){
                return $test_str;
            }
        ));

        $controller = new PMVC\ActionController();
        if( $controller->setMapping($b->getMappings()) ){
            $result = $controller->process();
        }
        $this->assertEquals($test_str,$result);
    }
}

