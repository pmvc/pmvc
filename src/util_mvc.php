<?php
namespace PMVC;

/**
 * PMVC
 */
function getC(){
    return option('get',CONTROLLER); 
}

function u($job,$url=null){
    return call_plugin(
        'url'
        ,'actionToUrl'
        ,array($job,$url)
    );
}

/**
 * files
 */
function transparent($name,$app=null){
    if(is_null($app)){
        $app = getC()->getApp();
    }
    $folder = getC()->getAppParent();
    if(!$folder){
        return $name;
    }
    $appFile = lastSlash($folder).$app.'/'.$name;
    $appFile = realpath($appFile); 
    if($appFile){
        return $appFile;
    }else{
        return $name;
    }
}

