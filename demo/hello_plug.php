<?php
include_once('../vendor/autoload.php');
PMVC\Load::mvc();

#cache
PMVC\plug('cache_header')->nocache();



$a =& PMVC\plug('file-list');

var_dump("111",$a,'111');
PMVC\unplug('file-list');
$a = PMVC\plug('file-list');

var_dump("111",$a,'111');
var_dump(time());

