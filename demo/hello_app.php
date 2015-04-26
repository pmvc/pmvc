<?php
use PMVC\ActionController as mvc;
include_once('/home/sys/web/lib/pmvc/include.php');


#cache
include('/home/sys/web/lib/cache_header_helper.php');
$cacheHeader = new CacheHeaderHelper();
$cacheHeader->setCache(0);

define('ROOT_LIB','/home/sys/web/lib/');
error_reporting(E_ALL);



$options = array(
    _ROUTING=>'routing'
    ,_VIEW_ENGINE=>'html'
    ,_PLUGIN_FOLDER=>ROOT_LIB.'plugin/'
    ,_ERROR_ENABLE_LOG=>true
    ,_PLUGIN=>array(
        'routing'=>null
        ,'dev'=>null
        ,'error_trace'=>null
    )
);


$controller = new mvc($options);
$file = $controller->getAppFile("./hello_app");
$r=PMVC\l($file,array('b'));
if( $controller->setMapping($r->var['b']->getMappings()) ){
    $a = $controller->process();
}

