<?php
use PMVC\ActionController as mvc;
include_once('/home/sys/web/lib/pmvc/include.php');

PMVC\setPlugInFolder('/git/plugin');

#cache
PMVC\plug('cache-header')->disable();




$options = array(
    _ROUTING=>'routing'
    ,_VIEW_ENGINE=>'html'
    ,_ERROR_ENABLE_LOG=>true
    ,_ERROR_REPORTING=>E_ALL
    ,_PLUGIN=>array(
        'routing'=>null
        ,'debug'=>null
        ,'error-trace'=>null
    )
);


$controller = new mvc($options);
$file = $controller->getAppFile("./hello_app");
$r=PMVC\l($file,array('b'));
if(!$r->var['b']){
    die('No mappings found. File:'.__FILE__.' Line:'.__LINE__);
}
if( $controller->setMapping($r->var['b']->getMappings()) ){
    $a = $controller->process();
}

