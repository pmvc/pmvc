<?php
namespace PMVC;

/**
 * File
 */
function realPath($p){
    if(!$p){
        return false;
    }
    return run('\realpath',array($p));
}

function l($name,$compacts=null,$once=true){
    static $files=array();
    $name = realpath($name);
    if(!$name){
        return false;
    }
    if(!( $once && isset($files[$name]) )){
        $files[$name]=true;
        include($name);
    }
    $o = new \stdClass();
    if($compacts){
        $o->name = $name;
        $o->var = compact($compacts);
    }else{
        $o->name=$name;
    }
    return $o;
} 

function includeApp($name,$bTransparent=null){
    if(!$bTransparent){
        return realpath($name);
    }
    $transparent = run(__NAMESPACE__.'\transparent',array($name));
    $transparent = realpath($transparent);
    if($transparent){
        return $transparent;
    }else{
        return realpath($name);
    }
}

function load(
    $name
    ,$type='file'
    ,$dirs=null
    ,$defaultDir=null
    ,$compacts=null
    ,$once=true
    ,$isIncludeApp=null
){
    if(empty($name)){
        return 1;
    }
    /**
     * cache find in load case, if some case can't use cahce please use find directly
     */
    $file = run(__NAMESPACE__.'\find', array($name,$type,$dirs,$defaultDir,$isIncludeApp));
    if($file){
        if($once){
            $r=run(__NAMESPACE__.'\l', array($file,$compacts));
        }else{
            $r=l($file,$compacts,$once);
        }
        return $r;
    }else{
        return 2;
    }
}

function find($name,$type='file',$dirs=null,$defaultDir=null,$isIncludeApp=null){
    if(empty($dirs)){
        if(is_null($defaultDir)){
            switch($type){
                case 'function':
                    $defaultDir = array('include/');
                    break;
                case 'class':
                    $defaultDir = array('class/'); 
                    break;
            }
        }
        if(!empty($defaultDir)){
            $dirs = $defaultDir;
        }else{
            return run(__NAMESPACE__.'\includeApp',array(mergeName($name,null,$type),$isIncludeApp));
        }
    }
    $dirs = splitDir($dirs);
    foreach($dirs as $dirPath){
        if(!(realPath($dirPath))){
            continue;
        }
        $r = run(__NAMESPACE__.'\includeApp',array(mergeName($name,$dirPath,$type),$isIncludeApp));
        if(!$r && 'file'!=$type){
            $lowerCase = run(__NAMESPACE__.'\lowerCaseFile',array($name,$type));
            $r = run(__NAMESPACE__.'\includeApp',array(mergeName($lowerCase,$dirPath,$type),$isIncludeApp));
        }
        if($r){
            return $r;
        }
    }
    return false;
}

/**
 * String Util (Path or Folder parse) 
 */
function lastSlash($s){
    $s = str_replace('\\','/',$s);
    $c = strlen($s);
    if($c){
        $s.=($s[$c-1]!='/')?'/':'';
        return $s;
    }else{
        return '';
    }
}

function lowerCaseFile($name,$type='class'){
    $s = preg_split("/([A-Z])/", $name,-1,PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
    $k = '';
    for($i=1,$j=count($s);$i<$j;$i++){
        if(preg_match('/[A-Z]/',$s[$i])){
            $k.='_'.strtolower($s[$i]);
        }else{
            $k.=$s[$i];
        }
    }
    $k = $type.'.'.strtolower($s[0]).$k;
    return $k;
}

function splitDir($s){
    if(!is_string($s)){
        return $s;
    }
    return  split('[;:]', $s );
}

function mergeName($name,$dir=null,$type=null){
    if(strlen($dir)){
        $name = lastSlash($dir).$name;
    }
    if(!is_null($type) && 'file'!==$type){
        $name .= '.php';
    }
    return $name;
}
/**
 * Array Merge
 */
function array_merge(...$a){
    $new = array();
    foreach($a as $i){
        if(is_null($i)) continue;
        if(!is_array($i)){
            $new[] = $i;
        }else{
            foreach($i as $k=>$v){
                $new[$k]=$v;
            }
        }
    }
    return $new;
}

function array_merge_by_default($defaults,$settings){
    foreach($defaults as $k=>$v){
        if(isset($settings[$k])){
            $defaults[$k]=$settings[$k];
        }
    }
    return $defaults;
}

/**
 * Data access
 */
function set(&$a,$k,$v=null){
    if(is_array($k)){
        $a = array_merge($a,$k);
    }else{
        if(is_null($v)){
            $a[]=$k;
        }else{
            $a[$k] = $v;
        }
    }
}

function &get(&$a,$k=null,$default=null){
    if(is_null($k)){
        return $a;
    }else{
        if(is_array($k)){
            $r=array();
            foreach($k as $i){
                if(array_key_exists($i,$a)){
                    $r[$i]=&$a[$i];
                }
            }
            return $r;
        } 
        if(!isset($a[$k])){
            $a[$k] =  $default;
        }
        return $a[$k];
    }
}

function clean(&$a,$k=null){
    if(is_null($k)){
        $a=null;
        unset($a);
    }else{
        if(is_array($k)){
            $a=&$k;
        }else{
            unset($a[$k]);
        }
    }
}


/**
 * option 
 */
function &getOption($k=null){
   return option('get',$k); 
}

function &option($act,$k=null,$v=null){
    static $options=array();
    switch($act){
        case 'get':
            $return =& get($options,$k);
            break;
        case 'set':
            $return = set($options,$k,$v);
            break;
    }
    return $return;
}

/**
 * misc
 */
function d(...$params){
    call_plugin('debug','d',$params);
}

function log(...$params){
    call_plugin('error-trace','log',$params);
}

function toArray($p){
    if(!is_array($p)){
        $p=array($p);
    }
    return $p;
}

function hash(...$params){
    return md5(var_export($params, true));
}

function &run($func,$args){
    static $cache=array();
    $hash = hash($func,$args); 
    if(!isset($cache[$hash])){
        $cache[$hash] = call_user_func_array($func,$args);
    }
    return $cache[$hash];
}

function exists($v,$type){
    switch($type){
    case 'function':
        return function_exists($v);
    case 'class':
        return class_exists($v);
    case 'file':
        return realpath($v);
    case 'plugIn':
        $objs = &getOption(PLUGIN_INSTANCE); 
        return !empty($objs[$v]); 
    default:
        return null;
    }
}

/**
* @param $v 
* @param $type [array|string]
*/
function n($v,$type=null){
    if(is_array($v)){
        if(!is_null($type) && 'array'!=$type){return false;}
        return count($v);
    }else{
        return strlen($v);
    }
}


/**
 * plugin
 */
function setPlugInFolder($folders,$alias=array()){
    if (n($alias,'array')) {
        option('set',_PLUGIN_ALIAS,$alias); 
    }
    return option('set',_PLUGIN_FOLDERS,toArray($folders));
}

function addPlugInFolder($folders,$alias=array()){
    $folders = \array_merge(
        getOption(_PLUGIN_FOLDERS),
        toArray($folders)
    );
    $alias = array_merge(
        getOption(_PLUGIN_ALIAS),
        $alias
    );
    setPlugInFolder($folders,$alias);
}

/**
 * call_plug_func 
 */
function call_plugIn($plugIn,$func,$params){
    if(exists($plugIn,'plugIn')){
        return plug($plugIn)->$func(...$params);
    }
}

/**
 * unplug
 */
function unPlug($name){
    if(!$name){  return $name; }
    $objs =& getOption(PLUGIN_INSTANCE); 
    if(isset($objs[$name])){
        unset($objs[$name]);
    }
}

function getPlugInNames()
{
    $objs =& getOption(PLUGIN_INSTANCE); 
    if(is_array($objs)){
        return array_keys($objs);
    }
    return array();
}

function initPlugIn($arr) {
    if(is_array($arr)){
        foreach($arr as $plugIn=>$config){
            if(!exists($plugIn,'plugIn')){
                plug($plugIn,$config);
            }
        }
    }
}

function plug($name,$config=null){
    if(!$name){  return $name; }
    $objs =& getOption(PLUGIN_INSTANCE);
    if(isset($objs[$name])){
        $oPlugin=$objs[$name];
        if(!is_null($config)){
            $oPlugin->set($config);
        }
        return $oPlugin->update();
    }
    if( isset($config[_CLASS]) && class_exists($config[_CLASS]) ){
        $class = $config[_CLASS];
    }else{
        $file = null; 
        if(!isset($config[_FILE])){
            $alias = getOption(_PLUGIN_ALIAS);
            if(isset($alias[$name])){
                $file=$alias[$name];
            }
        }else{
            $file=$config[_FILE];
        }
        if( !is_null($file) && realpath($file) ){
            $r=run(__NAMESPACE__.'\l',array($file,_INIT_CONFIG));
        } else {
            $file = $name.'.php' ;
            $default_folders = getOption(_PLUGIN_FOLDERS);
            $folders = array();
            foreach ($default_folders as $folder) {
                $folders[] = lastSlash($folder).$name;
            }
            $r=load($file, 'file', $folders, null, _INIT_CONFIG, true, false);
        }
        $class = (2!==$r)?$r->var[_INIT_CONFIG][_CLASS]:false;
    }
    if(class_exists($class)){
        $oPlugin = new $class();
    }else{
        trigger_error( 'get undefined plugIn ('.$name.')' );
        unset($objs[$name]);
        $name=false;
        return $name;
    }
    $oPlugin->name=$name;
    $config = array_merge($r->var[_INIT_CONFIG],$config);
    if( !empty($config) ){
        $oPlugin->set($config);
    }
    $oPlugin->file = $r->name;
    $oPlugin->init();
    $objs[$name]=$oPlugin;
    return $oPlugin->update();
}

class PLUGIN extends HashMap 
{
    /**
     * @var string
     */
    var $name;
    var $file;
    function getDir(){
        return dirname($this->file).'/';
    }
    function init(){ }
    function update($observer=null,$state=null){
        if(!is_null($state) && method_exists($this,'on'.$state)){
            $r=call_user_func(
                array(&$this, 'on'.$state)
                ,$observer
                ,$state
            );
            return $r;
        }
        return $this; 
    }
}
