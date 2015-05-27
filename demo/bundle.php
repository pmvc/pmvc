<?php
include_once('../vendor/autoload.php');
PMVC\Load::plug();
#cache
PMVC\plug('cache_header')->nocache();

$p = '';
if(isset($_GET['p'])){
    $p=$_GET['p'].'.';
}
echo file_get_contents($p.'bundle.js');
