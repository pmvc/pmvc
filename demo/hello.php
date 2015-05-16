<?php
include_once('/home/sys/web/lib/pmvc/include.php');
PMVC\setPlugInFolder('/git/pmvc/pmvc-plugin');

#cache
PMVC\plug('cache-header')->nocache();



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

