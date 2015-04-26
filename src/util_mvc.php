<?php
namespace PMVC;

/**
 * PMVC
 */
function &getC(){
    return option('get',CONTROLLER); 
}


function getAppName(){
    return option('get',RUN_APP); 
}

function getAppFolder(){
    return option('get',RUN_APP_FOLDER); 
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
        $app = getAppName();
    }
    $folder = getAppFolder();
    if(!$folder){
        return $name;
    }
    $appFile = $folder.$app.'/'.$name;
    $appFile = realpath($appFile); 
    if($appFile){
        return $appFile;
    }else{
        return $name;
    }
}

