<?php
include_once('/home/sys/web/lib/pmvc/include_plug.php');
#cache
include('/home/sys/web/lib/cache_header_helper.php');
$cacheHeader = new CacheHeaderHelper();
$cacheHeader->setCache(0);


PMVC\setPlugInFolder('/git/plugin/');

$a =& PMVC\plug('file-list');

var_dump("111",$a,'111');
PMVC\unplug('file-list');
$a = PMVC\plug('file-list');

var_dump("111",$a,'111');
var_dump(time());

