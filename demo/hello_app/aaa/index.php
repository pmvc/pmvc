<?php

$b = new PMVC\MappingBuilder();
${_INIT_CONFIG}[_CLASS] = 'NewActionName';
${_INIT_CONFIG}[_INIT_BUILDER] = $b;

$b->addAction('index', array(
    _CLASS=>'NewActionName'
    ,_FORM=>'HelloVerify'
));
$b->addAction('index_slower', array(
    _CLASS=>'NewActionName'
    ,_FUNCTION=>'index_slower'
));


$b->addForward('home', array(
    _PATH=>'hello'
    ,_TYPE=>'view'
    ,_USE_THEME=>true
    ,_SLOWER=>'index_slower'
));


class NewActionName extends PMVC\Action
{
    function index($m, $f){
       $go = $m->get('home');
       $go->set('text','hello world');
       return $go;
    }

    function index_slower($m,$f){
        echo "this is slower";
    }

}

class HelloVerify extends PMVC\ActionForm 
{
    function validate(){
        PMVC\plug("adkjfa;lsdkjf");
    }
}



