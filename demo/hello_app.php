<?php
use PMVC\ActionController as mvc;
include_once('/home/sys/web/lib/pmvc/include.php');

PMVC\setPlugInFolder('/git/pmvc-plugin');

#cache
PMVC\plug('cache-header')->nocache();




$options = array(
    _ROUTING=>'routing'
    ,_VIEW_ENGINE=>'html'
    ,_ERROR_ENABLE_LOG=>true
    ,_ERROR_REPORTING=>E_ALL
    ,_TEMPLATE_DIR=>'/git/pmvc-theme/hello-theme'
    ,_PLUGIN=>array(
        'routing'=>null
        ,'debug'=>null
        ,'error-trace'=>null
    )
);


$controller = new mvc($options);
if($controller->plugApp('/git/pmvc-app')){
    $a = $controller->process();
}

