<?php
include_once('../vendor/autoload.php');
PMVC\Load::mvc();
use PMVC\ActionController as mvc;

#cache
PMVC\plug('cache_header')->nocache();




$options = array(
    _ROUTING=>'routing'
    ,_VIEW_ENGINE=>'html'
    ,_ERROR_ENABLE_LOG=>true
    ,_ERROR_REPORTING=>E_ALL
    ,_TEMPLATE_DIR=>'/git/pmvc-git/pmvc-theme/hello-theme'
    ,_PLUGIN=>array(
        'routing'=>null
        ,'debug'=>null
        ,'error_trace'=>null
    )
);


$controller = new mvc($options);
if($controller->plugApp('/git/pmvc-git/pmvc-app')){
    $a = $controller->process();
}

