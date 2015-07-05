<?php
/**
 * PMVC
 *
 * PHP version 5
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  GIT: <git_id>
 * @link     http://pear.php.net/package/PackageName
 */
namespace PMVC;

/**
 * File <--------------
 */

/**
 * RealPath
 * 
 * @param string $p parameters
 *
 * @return string
 */
function realPath($p)
{
    if (!$p) {
        return false;
    }
    return run('\realpath', array($p));
}

/**
 * Same with include, but self manage include_once 
 * and make global variable to local variable
 *
 * @param string  $name     file name 
 * @param string  $compacts decide extrac files variable 
 * @param boolean $once     if incldue once 
 *
 * @return mixed 
 */
function l($name, $compacts=null, $once=true)
{
    static $files=array();
    $real = realpath($name);
    if (!$once || !isset($files[$real])) {
        include $name;
        $files[$real]=true;
    }
    $o = new \stdClass();
    $o->name = $real;
    if ($compacts) {
        $o->var = compact($compacts);
    }
    return $o;
}

/**
 * Include app folder 
 *
 * @param string $name         file name 
 * @param string $bTransparent Transparent app folder
 *
 * @return mixed 
 */
function includeApp($name, $bTransparent=null)
{
    if (!$bTransparent) {
        return realpath($name);
    }
    $transparent = run(__NAMESPACE__.'\transparent', array($name));
    $transparent = realpath($transparent);
    if ($transparent) {
        return $transparent;
    } else {
        return realpath($name);
    }
}

/**
 * Smart Load 
 *
 * @param string  $name         name 
 * @param string  $type         type [file|class|function] 
 * @param mixed   $dirs         dirs 
 * @param string  $defaultDir   default folder 
 * @param string  $compacts     decide extrac files variable 
 * @param boolean $once         if incldue once 
 * @param boolean $isIncludeApp search for application folder 
 *
 * @return mixed 
 */
function load(
    $name,
    $type='file',
    $dirs=null,
    $defaultDir=null,
    $compacts=null,
    $once=true,
    $isIncludeApp=null
) {
    if (empty($name)) {
        return 1;
    }
    /**
     * Cache find in load case, if some case can't use cahce please use find directly
     */
    $file = run(
        __NAMESPACE__.'\find',
        array(
            $name,
            $type,
            $dirs,
            $defaultDir,
            $isIncludeApp
        )
    );
    if ($file) {
        if ($once) {
            $r=run(__NAMESPACE__.'\l', array($file,$compacts));
        } else {
            $r=l($file, $compacts, $once);
        }
        return $r;
    } else {
        return 2;
    }
}


/**
 * Smart find 
 *
 * @param string  $name         name 
 * @param string  $type         type [file|class|function] 
 * @param mixed   $dirs         dirs 
 * @param string  $defaultDir   default folder 
 * @param boolean $isIncludeApp search for application folder 
 *
 * @return mixed 
 */
function find($name, $type='file', $dirs=null, $defaultDir=null, $isIncludeApp=null)
{
    if (empty($dirs)) {
        if (is_null($defaultDir)) {
            switch ($type) {
            case 'function':
                $defaultDir = array('include/');
                break;
            case 'class':
                $defaultDir = array('class/');
                break;
            }
        }
        if (!empty($defaultDir)) {
            $dirs = $defaultDir;
        } else {
            return run(
                __NAMESPACE__.'\includeApp',
                array(
                    mergeName($name, null),
                    $isIncludeApp
                )
            );
        }
    }
    $dirs = splitDir($dirs);
    foreach ($dirs as $dirPath) {
        if (!(realPath($dirPath))) {
            continue;
        }
        $r = run(
            __NAMESPACE__.'\includeApp',
            array(
                mergeName($name, $dirPath),
                $isIncludeApp
            )
        );
        if (!$r && 'file'!=$type) {
            $lowerCase = run(__NAMESPACE__.'\lowerCaseFile', array($name,$type));
            $r = run(
                __NAMESPACE__.'\includeApp',
                array(
                    mergeName($lowerCase, $dirPath),
                   $isIncludeApp
                )
            );
        }
        if ($r) {
            return $r;
        }
    }
    return false;
}

/**
 * String Util (Path or Folder parse) <!---
 */

/**
 * Auto append last slash for dir or file 
 *
 * @param string $s folder or file name 
 *
 * @return string 
 */
function lastSlash($s)
{
    $s = str_replace('\\', '/', $s);
    $c = strlen($s);
    if ($c) {
        $s.=($s[$c-1]!='/')?'/':'';
        return $s;
    } else {
        return '';
    }
}

/**
 * Change file name for Uppder case to lower case 
 *
 * @param string $name file name 
 * @param string $type [class|function] 
 *
 * @return string 
 */
function lowerCaseFile($name, $type='class')
{
    $s = preg_split(
        "/([A-Z])/", 
        $name, 
        -1, 
        PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY
    );
    $k = '';
    for ($i=1, $j=count($s);$i<$j;$i++) {
        if (preg_match('/[A-Z]/', $s[$i])) {
            $k.='_'.strtolower($s[$i]);
        } else {
            $k.=$s[$i];
        }
    }
    $k = $type.'.'.strtolower($s[0]).$k;
    return $k;
}

/**
 * Split folder string to arrays 
 *
 * @param string $s folder string 
 *
 * @return array 
 */
function splitDir($s)
{
    if (!is_string($s)) {
        return $s;
    }
    return  split('[;:]', $s);
}

/**
 * Merge name 
 *
 * @param string $name name 
 * @param string $dir  dir 
 *
 * @return string 
 */
function mergeName($name, $dir=null)
{
    if (!empty($dir)) {
        $name = lastSlash($dir).$name;
    }
    return $name;
}

/**
 * Array Merge
 *
 * @return array
 */
function Array_merge()
{
    $a = func_get_args();
    $new = $a[0];
    for ($i=1,$j=count($a);$i<$j;$i++) {
        if (is_null($a[$i])) {
            continue;
        }
        if (!is_array($a[$i])) {
            $new[] = $a[$i];
        } else {
            foreach ($a[$i] as $k=>$v) {
                $new[$k]=$v;
            }
        }
    }
    return $new;
}

/**
* Merge array with a default set 
*
* @param array $defaults default 
* @param array $settings setting
*
* @return array 
*/
function mergeDefault($defaults, $settings)
{
    foreach ($defaults as $k=>$v) {
        if (isset($settings[$k])) {
            $defaults[$k]=$settings[$k];
        }
    }
    return $defaults;
}

/**
 * Data access <!---
 */

/**
* Magic Set function 
*
* @param array $a array 
* @param mixed $k key 
* @param mixed $v value 
*
* @return mixed 
*/
function set(&$a, $k, $v=null)
{
    if (is_array($k)) { //merge by new array
        $a = array_merge($a, $k);
    } else {
        if (is_null($v)) { //append value when no-assign key
            $a[]=$k;
        } else { //exactly set key and value
            $a[$k] = $v;
        }
    }
}

/**
* Magic Get function 
*
* @param array $a       array 
* @param mixed $k       key 
* @param mixed $default default 
*
* @return mixed 
*/
function &get(&$a, $k=null, $default=null)
{
    if (is_null($k)) { //return all
        return $a;
    } else {
        if (is_array($k)) { //return by keys
            $r=array();
            foreach ($k as $i) {
                if (array_key_exists($i, $a)) {
                    $r[$i]=&$a[$i];
                }
            }
            return $r;
        }
        //return one
        if (!isset($a[$k])) { //return default
            $a[$k]=$default;
        }
        return $a[$k]; //return exactly value
    }
}

/**
* Magic Clean function 
*
* @param array $a array 
* @param mixed $k key 
*
* @return void 
*/
function clean(&$a, $k=null)
{
    if (is_null($k)) { //clean all
        $a=null;
        unset($a);
    } else {
        if (is_array($k)) { //replace
            $a=&$k;
        } else {
            unset($a[$k]); //clean by key
        }
    }
}


/**
 * Option <!-----------
 */

/**
* Get Option
*
* @param mixed $k which want to get 
*
* @return string hash result 
*/
function &getOption($k=null)
{
    return option('get', $k);
}

/**
* Global option for get/set 
*
* @param string $act [set|get] 
* @param mixed  $k   key 
* @param mixed  $v   value 
*
* @return mixed 
*/
function &option($act, $k=null, $v=null)
{
    static $options=array();
    switch ($act) {
    case 'get':
        $return =& get($options, $k);
        break;
    case 'set':
        $return = set($options, $k, $v);
        if (is_string($k)) {
            $k = array($k=>$v);
        }
        if (is_array($k)) {
            foreach ($k as $i=>$v) {
                if (is_string($v)) {
                    putenv($i.'='.$v);
                }
            }
        }
        break;
    }
    return $return;
}

/**
 * Misc <!----------
 */

/**
 * Dump for debug 
 *
 * @return vod
 */
function d()
{
    $params = func_get_args();
    call_plugin('debug', 'd', $params);
}

/**
 * Log for debug
 *
 * @return vod
 */
function log()
{
    $params = func_get_args();
    call_plugin('error-trace', 'log', $params);
}

/**
* Keep string and array both in array type 
*
* @param mixed $p parameters 
*
* @return string hash result 
*/
function toArray($p)
{
    if (!is_array($p)) {
        $p=array($p);
    }
    return $p;
}

/**
* Hash function 
*
* @return string hash result 
*/
function hash()
{
    $params = func_get_args();
    return md5(var_export($params, true));
}

/**
* Cache function run result 
*
* @param mixed $func run function 
* @param mixed $args parameters 
*
* @return boolean 
*/
function &run($func, $args)
{
    static $cache=array();
    $hash = hash($func, $args);
    if (!isset($cache[$hash])) {
        $cache[$hash] = call_user_func_array($func, $args);
    }
    return $cache[$hash];
}

/**
* Check exists 
*
* @param mixed $v    value
* @param mixed $type [array|string]
*
* @return boolean 
*/
function exists($v, $type)
{
    switch (strtolower($type)) {
    case 'function':
        return function_exists($v);
    case 'class':
        return class_exists($v);
    case 'file':
        return realpath($v);
    case 'plugin':
        $objs = &getOption(PLUGIN_INSTANCE);
        return !empty($objs[$v]);
    default:
        return null;
    }
}

/**
* Count number
*
* @param mixed $v    value
* @param mixed $type [array|string]
*
* @return mixed
*/
function n($v, $type=null)
{
    if (is_array($v)) {
        if (!is_null($type) && 'array'!=$type) {
            return false;
        }
        return count($v);
    } else {
        return strlen($v);
    }
}

/**
 * Plugins <-----------------------------------
 */

/**
 * Set PlugIn Folder
 *
 * @param array $folders plug-in folders 
 * @param array $alias   plug-in alias 
 *
 * @return mixed
 */
function setPlugInFolder($folders, $alias=array())
{
    if (n($alias, 'array')) {
        option('set', PLUGIN_ALIAS, $alias);
    }
    return option('set', PLUGIN_FOLDERS, toArray($folders));
}

/**
 * Add PlugIn Folder
 * 
 * @param array $folders plug-in folders 
 * @param array $alias   plug-in alias 
 * 
 * @return mixed
 */
function addPlugInFolder($folders, $alias=array())
{
    $folders = \array_merge(
        getOption(PLUGIN_FOLDERS),
        toArray($folders)
    );
    $alias = array_merge(
        getOption(PLUGIN_ALIAS),
        $alias
    );
    setPlugInFolder($folders, $alias);
}

/**
 * Call Plug-In 
 * 
 * @param sring $plugIn plug-in name 
 * @param sring $func   plug-in function 
 * @param sring $args   plug-in function parameters 
 * 
 * @return mixed
 */
function Call_plugIn($plugIn, $func, $args)
{
    if (exists($plugIn, 'plugin')) {
        return call_user_func_array(
            array(
                plug($plugIn),
                $func
            ),
            $args
        );
    }
}

/**
 * Unplug
 *
 * @param sring $name plug-in name 
 *
 * @return mixed \PMVC\PlugIn
 */
function unPlug($name)
{
    if (!$name) {
        return $name;
    }
    return rePlug($name, null);
}

/**
 * Re plug
 *
 * @param sring  $name   plug-in name 
 * @param PlugIn $object plug-in plugin instance 
 * 
 * @return mixed \PMVC\PlugIn
 */
function rePlug($name, PlugIn $object)
{
    $objs =& getOption(PLUGIN_INSTANCE);
    $objs[$name]=$object;
    return $objs[$name];
}

/**
 * Get PlugIn Names
 *
 * @return mixed 
 */
function getPlugInNames()
{
    $objs =& getOption(PLUGIN_INSTANCE);
    if (is_array($objs)) {
        return array_keys($objs);
    }
    return array();
}

/**
 * Init PlugIn
 *
 * @param array $arr plug-in array 
 *
 * @return void 
 */
function initPlugIn($arr)
{
    if (is_array($arr)) {
        foreach ($arr as $plugIn=>$config) {
            if (!exists($plugIn, 'plugin')) {
                plug($plugIn, $config);
            }
        }
    }
}

/**
 * Plug 
 *
 * @param string $name   plugin name 
 * @param array  $config plugin configs
 *
 * @return mixed
 */
function plug($name, $config=null)
{
    if (!$name) {
        return $name;
    }
    $objs =& getOption(PLUGIN_INSTANCE);
    if (isset($objs[$name])) {
        $oPlugin=$objs[$name];
        if (!is_null($config)) {
            set($oPlugin, $config);
        }
        return $oPlugin->update();
    }
    if (isset($config[_CLASS]) && class_exists($config[_CLASS])) {
        $class = $config[_CLASS];
    } else {
        $file = null;
        if (!isset($config[_PLUGIN_FILE])) {
            $alias = getOption(PLUGIN_ALIAS);
            if (isset($alias[$name])) {
                $file=$alias[$name];
            }
        } else {
            $file=$config[_PLUGIN_FILE];
        }
        if (!is_null($file) && realpath($file)) {
            $r=run(__NAMESPACE__.'\l', array($file,_INIT_CONFIG));
        } else {
            $file = $name.'.php' ;
            $default_folders = getOption(PLUGIN_FOLDERS);
            $folders = array();
            foreach ($default_folders as $folder) {
                $folders[] = lastSlash($folder).$name;
            }
            $r=load($file, 'file', $folders, null, _INIT_CONFIG, true, false);
        }
        $class = (!empty($r->var[_INIT_CONFIG][_CLASS]))
            ? $r->var[_INIT_CONFIG][_CLASS]
            :false;
    }
    if (class_exists($class)) {
        $oPlugin = new $class();
    } else {
        trigger_error('get undefined plugIn ('.$name.')');
        unset($objs[$name]);
        $name=false;
        return $name;
    }
    $oPlugin->name=$name;
    if (!empty($r)) {
        $config = array_merge($r->var[_INIT_CONFIG], $config);
        $oPlugin->file = $r->name;
    }
    if (!empty($config)) {
        set($oPlugin, $config);
    }
    $oPlugin->init();
    $objs[$name]=$oPlugin;
    return $oPlugin->update();
}

