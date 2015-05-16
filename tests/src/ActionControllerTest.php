<?php
class ActionControllerTest extends PHPUnit_Framework_TestCase
{
    function testConstruct(){
        $a = array(
            'xxx'=>'yyy'
        );
        $mvc = new PMVC\ActionController($a); 
        $this->assertEquals('yyy',PMVC\getOption('xxx'));
    }

    function testProcess(){
        $b = new PMVC\MappingBuilder();
        $b->addAction('index', array(
            _CLASS=>'FakeClass'
        ));
        $b->addForward('home', array(
            _PATH=>'hello'
            ,_TYPE=>'view'
            ,_LAZY_OUTPUT=>'xxx'
        ));
        $mvc = $this->getMock('\PMVC\ActionController',array('execute'),array(array()));
        $mvc->setMapping($b->getMappings());
        $mvc->expects($this->exactly(2))
           ->method('execute')
           ->will($this->returnValue((object)array(
                'lazyOutput'=>true
            )));
        $mvc->process();
    }
}

class FakeClass extends PMVC\Action
{
    function index($m, $f) {
       $go = $m->get('home');
       return $go;
    }
}
