<?php
include('/home/sys/web/lib/cache_header_helper.php');
$cacheHeader = new CacheHeaderHelper();
$cacheHeader->publicCache(0);
$p = '';
if(isset($_GET['p'])){
    $p=$_GET['p'].'.';
}
echo file_get_contents($p.'bundle.js');
