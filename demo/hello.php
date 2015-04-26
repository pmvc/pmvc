<?php
include_once('/home/sys/web/lib/pmvc/include.php');
include('/home/sys/web/lib/cache_header_helper.php');

#cache
$cacheHeader = new CacheHeaderHelper();
$cacheHeader->setCache(0);


$b = new PMVC\MappingBuilder();
$b->addAction('index', array(
    _CLASS=>'NewActionName'
));


class NewActionName extends PMVC\Action
{
    function index($m, $f) {
        return 'Hello World!';
    }
}

$controller = new PMVC\ActionController();
if( $controller->setMapping($b->getMappings()) ){
    $a = $controller->process();
    var_dump($a);
}

