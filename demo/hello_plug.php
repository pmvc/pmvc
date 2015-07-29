<?php
include_once('../vendor/autoload.php');
PMVC\Load::mvc();

#cache
PMVC\plug('cache_header')->nocache();


$plug = 'cache_header';
$a = PMVC\plug($plug);

var_dump("111",$a[_PLUGIN],'111');
PMVC\unplug($plug);


var_dump("222",$a[_PLUGIN],'222');
$a = PMVC\plug($plug);

var_dump("111",$a[_PLUGIN],'111');
var_dump(time());

