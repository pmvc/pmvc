<?php

include 'vendor/autoload.php';
PMVC\Load::mvc();

$yo = PMVC\plug('yo');
$yo->get('/hello/{name}', function ($m, $f) {
    echo 'hello: '.$f->get('name');
});

$yo->process();
