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
}
