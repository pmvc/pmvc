<?php
include_once('../vendor/autoload.php');
PMVC\Load::mvc();

#cache
PMVC\plug('cache_header')->nocache();

$b = new PMVC\MappingBuilder();
$b->addAction('index', array(
    _FUNCTION=>function(){
        return 'Hello World!';
    }
));

$controller = new PMVC\ActionController();
if( $controller->setMapping($b->getMappings()) ){
    $a = $controller->process();
    var_dump($a);
}

