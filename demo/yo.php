<?php
include('vendor/autoload.php');
include_once('/home/sys/web/lib/pmvc/include.php');
PMVC\setPlugInFolder('/git/pmvc/pmvc-plugin');

$yo = PMVC\plug('yo');

$yo->get('/hello/{name}',function($m,$f){
    echo "hello: ".$f->get('name');
});

$yo->process();


