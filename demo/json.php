<?php
include_once('../vendor/autoload.php');
PMVC\Load::mvc();
use PMVC\ActionController as mvc;

#cache
PMVC\plug('cache-header')->nocache();



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
if($controller->plugApp('/git/pmvc-app')){
    $a = $controller->process();
}
