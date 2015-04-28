<?php
use PMVC\ActionController as mvc;
include_once('/home/sys/web/lib/pmvc/include.php');

PMVC\setPlugInFolder('/git/plugin/');

#cache
PMVC\plug('cache-header')->disable();




$options = array(
    _ROUTING=>'routing'
    ,_VIEW_ENGINE=>'json'
    ,_ERROR_ENABLE_LOG=>true
    ,_ERROR_REPORTING=>E_ALL
    ,_PLUGIN=>array(
        'routing'=>null
    )
);


$controller = new mvc($options);
$file = $controller->getAppFile("./hello_app");
$r=PMVC\l($file,array('b'));
if( $controller->setMapping($r->var['b']->getMappings()) ){
    $a = $controller->process();
}

