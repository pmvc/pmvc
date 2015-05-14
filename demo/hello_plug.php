<?php
include_once('/home/sys/web/lib/pmvc/include_plug.php');

PMVC\setPlugInFolder('/git/pmvc-plugin');

#cache
PMVC\plug('cache-header')->nocache();



$a =& PMVC\plug('file-list');

var_dump("111",$a,'111');
PMVC\unplug('file-list');
$a = PMVC\plug('file-list');

var_dump("111",$a,'111');
var_dump(time());

