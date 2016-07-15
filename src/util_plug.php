<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category CategoryName
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @version GIT: <git_id>
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
namespace PMVC;

use ArrayAccess;

option(
    'set',
    PLUGIN_INSTANCE,
    new HashMap()
);

/**
 * File <!---.
 */

/**
 * RealPath.
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

    return run('\realpath', [$p]);
}

/**
 * Same with include, but self manage include_once
 * and make global variable to local variable.
 *
 * @param string $name     file name
 * @param array  $compacts decide extrac files variable
 * @param bool   $once     if incldue once
 *
 * @return mixed
 */
function l($name, $compacts = null, $once = true)
{
    $real = realpath($name);
    if (!$real) {
        return !trigger_error('File not found. ['.$name.']');
    }
    if ($once) {
        return run(__NAMESPACE__.'\_l', [$real, $compacts]);
    } else {
        return _l($real, $compacts);
    }
}

/**
 * Private funciton for l.
 *
 * @param string $name     file name
 * @param array  $compacts decide extrac files variable
 *
 * @return mixed
 */
function _l($name, $compacts = null)
{
    include $name;
    $o = new \stdClass();
    $o->name = $name;
    if ($compacts) {
        $o->var = compact($compacts);
    }

    return $o;
}

/**
 * Prepend app folder.
 *
 * @param string $name         file name
 * @param string $bTransparent Transparent app folder
 *
 * @return mixed
 */
function prependApp($name, $bTransparent = null)
{
    if (!$bTransparent || !exists('controllder', 'plugin')) {
        return realpath($name);
    }

    return run(__NAMESPACE__.'\transparent', [$name]);
}

/**
 * Smart Load.
 *
 * @param string $name        name
 * @param mixed  $dirs        dirs
 * @param mixed  $compacts    decide extrac files variable
 * @param bool   $once        if incldue once
 * @param bool   $bPrependApp search for application folder
 *
 * @return mixed
 */
function load(
    $name,
    $dirs = null,
    $compacts = null,
    $once = true,
    $bPrependApp = null
) {
    if (empty($name)) {
        return 1;
    }
    /*
     * Cache find in load case, if some case can't use cahce please use find directly
     */
    $file = run(
        __NAMESPACE__.'\find',
        [
            $name,
            $dirs,
            $bPrependApp,
        ]
    );
    if ($file) {
        return l($file, $compacts, $once);
    } else {
        return 2;
    }
}

/**
 * Smart find.
 *
 * @param string $name        name
 * @param mixed  $dirs        dirs
 * @param bool   $bPrependApp search for application folder
 *
 * @return mixed
 */
function find($name, $dirs = null, $bPrependApp = null)
{
    $dirs = splitDir($dirs);
    foreach ($dirs as $dirPath) {
        if (!realpath($dirPath)) {
            continue;
        }
        $r = prependApp(mergeFileName($name, $dirPath), $bPrependApp);
        if ($r) {
            return $r;
        }
    }

    return false;
}

/**
 * Folder store.
 *
 * @param string $type    Folder's group
 * @param array  $folders Which folder need store
 * @param array  $alias   Which alias need store
 * @param bool   $clean   Reset folder by type
 *
 * @return mixed
 */
function folders($type, array $folders = [], array $alias = [], $clean = null)
{
    static $_folders = [];
    static $_alias = [];
    if (!isset($_folders[$type]) || $clean) {
        $_folders[$type] = [];
        $_alias[$type] = [];
    }
    $_folders[$type] = \array_merge(
        $_folders[$type],
        $folders
    );
    $_alias[$type] = \array_merge(
        $_alias[$type],
        $alias
    );

    return [
       'folders' => array_reverse($_folders[$type]),
       'alias'   => $_alias[$type],
    ];
}

/**
 * String Util (Path or Folder parse) <!---.
 */

/**
 * Merge name.
 *
 * @param string $name name
 * @param string $dir  dir
 *
 * @return string
 */
function mergeFileName($name, $dir = null)
{
    if (!empty($dir)) {
        $name = lastSlash($dir).$name;
    }

    return $name;
}

/**
 * Auto append last slash for dir or file.
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
        $s .= ($s[$c - 1] != '/') ? '/' : '';

        return $s;
    } else {
        return '';
    }
}

/**
 * Multi explode.
 *
 * @param mixed  $delimiters string or array
 * @param string $s          string
 *
 * @return array
 */
function split($delimiters, $s)
{
    if (!is_string($s)) {
        return $s;
    }
    if (is_string($delimiters)) {
        $delimiters = str_split($delimiters);
    }
    $s = str_replace($delimiters, $delimiters[0], $s);

    return explode($delimiters[0], $s);
}

/**
 * Split folder string to arrays.
 *
 * @param string $s folder string
 *
 * @return array
 */
function splitDir($s)
{
    return split(';:', $s);
}

/**
 * Hash function.
 *
 * @return string hash result
 */
function hash()
{
    $params = func_get_args();

    return md5(var_export($params, true));
}

/**
 * Form Json.
 *
 * @param string $s origin string
 *
 * @return mixed return json_decode or origin value
 */
function &fromJson($s)
{
    if (!is_string($s)) {
        return $s;
    }
    $args = func_get_args();
    $json = call_user_func_array('json_decode', $args);
    if (json_last_error() === JSON_ERROR_NONE) {
        return $json;
    } else {
        return $s;
    }
}

/**
 * Array Util <!---.
 */

/**
 * Check an array or string equal one value.
 *
 * @param mixed  $haystack search on array or string
 * @param string $needle   search keyword
 *
 * @return array
 */
function hasKey($haystack, $needle)
{
    if ($haystack === $needle
        || isset($haystack[$needle])
    ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Array Replace (The numeric key will be overwrite not append).
 *
 * @return array
 */
function arrayReplace()
{
    $a = func_get_args();
    $new = $a[0];
    if (!isArray($new)) {
        return !trigger_error('Param1 need be an array. '.print_r($new, true));
    }
    for ($i = 1, $j = count($a); $i < $j; $i++) {
        if (is_null($a[$i])) {
            continue;
        }
        if (!isArray($a[$i])) {
            $new[] = $a[$i];
        } else {
            foreach ($a[$i] as $k => $v) {
                $new[$k] = $v;
            }
        }
    }

    return $new;
}

/**
 * Keep string and array both in array type.
 *
 * @param mixed $p parameters
 *
 * @return string hash result
 */
function toArray($p)
{
    if (is_null($p)) {
        return [];
    }
    if (!is_array($p)) {
        $p = [$p];
    }

    return $p;
}

/**
 * Check is a ArrayAccess Object.
 *
 * @param mixed $obj any object
 *
 * @return bool
 */
function isArrayAccess($obj)
{
    return $obj instanceof ArrayAccess;
}

/**
 * Check is ArrayAccess or array.
 *
 * @param mixed $obj any object
 *
 * @return bool
 */
function isArray($obj)
{
    return  is_array($obj) || isArrayAccess($obj);
}

/**
 * Safe get multi layer array value.
 *
 * @param array $arr     array
 * @param array $path    array's path
 * @param mixed $default if value not exists, return default value
 *
 * @return bool
 */
function value($arr, $path, $default = null)
{
    if (!isArray($path)) {
        return !trigger_error('[PMVC\value] Path is not array');
    }
    $a = &$arr;
    foreach ($path as $p) {
        if (!isArray($a)) {
            $a = (array) $a;
        }
        if (isset($a[$p])) {
            $a = $a[$p];
        } else {
            return $default;
        }
    }

    return $a;
}

/**
 * Data access <!---.
 */

/**
 * Magic Clean function.
 *
 * @param array $a array
 * @param mixed $k key
 *
 * @return void
 */
function clean(&$a, $k = null)
{
    if (is_null($k)) { //clean all
        if (isArrayAccess($a)) {
            return $a->offsetUnset(null);
        } else {
            $a = [];
            unset($a);

            return;
        }
    } else {
        if (isArray($k)) { //replace
            if (isArrayAccess($a)) {
                foreach ($k as $k1 => $v1) {
                    $a->offsetSet($k1, $v1);
                }

                return true;
            } else {
                return $a = $k;
            }
        } else {
            unset($a[$k]); //clean by key
            return;
        }
    }
}

/**
 * Magic Get function.
 *
 * @param array $a       array
 * @param mixed $k       key
 * @param mixed $default default
 *
 * @return mixed
 */
function &get(&$a, $k = null, $default = null)
{
    if (is_null($k)) { //return all
        if (isArrayAccess($a)) {
            $r = $a->offsetGet();

            return $r;
        }

        return $a;
    } else {
        if (is_array($k)) { //return by keys
            if (isArrayAccess($a)) {
                return $a->offsetGet($k);
            } else {
                $r = [];
                foreach ($k as $i) {
                    if (isset($a[$i])) {
                        $r[$i] = &$a[$i];
                    }
                }

                return $r;
            }
        }
        //return one
        if (!isset($a[$k])) { //return default
            set($a, $k, $default);
        }
        if (isArrayAccess($a)) {
            $v = $a->offsetGet($k);

            return $v;
        } else {
            return $a[$k]; //return exactly value
        }
    }
}

/**
 * Magic Set function.
 *
 * @param array $a array
 * @param mixed $k key
 * @param mixed $v value
 *
 * @return mixed
 */
function set(&$a, $k, $v = null)
{
    if (is_null($k) && is_null($v)) {
        return false;
    } elseif (isArray($k) || is_object($k)) {
        if (is_object($k)) {
            $k = (array) $k;
        }

        return $a = arrayReplace($a, $k); //merge by new array
    } elseif (is_null($k)) {
        return $a[] = $v; //append value when no-assign key
    } else {
        return $a[$k] = $v; //exactly set key and value
    }
}

/**
 * Option <!---.
 */

/**
 * Get Option.
 *
 * @param mixed $k       which want to get
 * @param mixed $default value or default
 *
 * @return string hash result
 */
function &getOption($k = null, $default = null)
{
    return option('get', $k, $default);
}

/**
 * Global option for get/set.
 *
 * @param string $act [set|get]
 * @param mixed  $k   key
 * @param mixed  $v   value or default
 *
 * @return mixed
 */
function &option($act, $k = null, $v = null)
{
    static $options = [];
    switch ($act) {
    case 'get':
        $return = &get($options, $k, $v);
        break;
    case 'set':
        $return = set($options, $k, $v);
        break;
    }

    return $return;
}

/**
 * Misc <!---.
 */

/**
 * Dump for debug.
 *
 * @return void
 */
function d()
{
    callPlugin('debug', 'd', func_get_args());
}

/**
 * Log for debug.
 *
 * @return void
 */
function log()
{
    callPlugin('error_trace', 'log', func_get_args());
}

/**
 * Develop.
 *
 * @return void
 */
function dev()
{
    callPlugin('dev', 'dump', func_get_args());
}

/**
 * Is develop.
 *
 * @return bool
 */
function isDev()
{
    static $bool;
    if (is_null($bool)) {
        $bool = !empty(callPlugin('dev', 'isDev', func_get_args()));
    }

    return $bool;
}

/**
 * Cache function run result.
 *
 * @param mixed $func run function
 * @param mixed $args parameters
 *
 * @return bool
 */
function run($func, $args)
{
    static $cache = [];
    $hash = hash($func, $args);
    if (!isset($cache[$hash])) {
        $cache[$hash] = false;
        $cache[$hash] = call_user_func_array($func, $args);
    }

    return $cache[$hash];
}

/**
 * Check exists.
 *
 * @param mixed $v    value
 * @param mixed $type [array|string]
 *
 * @return bool
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
        $objs = getOption(PLUGIN_INSTANCE);

        return !empty($objs[$v]);
    default:
        return;
    }
}

/**
 * Plugins <!---.
 */

/**
 * Set PlugIn Folder.
 *
 * @param array $folders plug-in folders
 * @param array $alias   plug-in alias
 *
 * @return mixed
 */
function setPlugInFolders(array $folders, array $alias = [])
{
    return folders(_PLUGIN, $folders, $alias, true);
}

/**
 * Add PlugIn Folder.
 *
 * @param array $folders plug-in folders
 * @param array $alias   plug-in alias
 *
 * @return mixed
 */
function addPlugInFolders(array $folders, array $alias = [])
{
    return folders(_PLUGIN, $folders, $alias);
}

/**
 * Call Plug-In.
 *
 * @param sring $plugIn plug-in name
 * @param sring $func   plug-in function
 * @param sring $args   plug-in function parameters
 *
 * @return mixed
 */
function callPlugin($plugIn, $func, $args = [])
{
    if (exists($plugIn, 'plugin')) {
        return call_user_func_array(
            [
                plug($plugIn),
                $func,
            ],
            $args
        );
    }
}

/**
 * Unplug.
 *
 * @param sring $name plug-in name
 *
 * @return mixed PlugIn
 */
function unPlug($name)
{
    if (!$name) {
        return $name;
    }
    $objs = getOption(PLUGIN_INSTANCE);
    $plug = $objs[$name];
    $objs[$name] = null;
    unset($objs[$name]);

    return $plug;
}

/**
 * Re plug.
 *
 * @param sring $name   plug-in name
 * @param mixed $object plug-in plugin instance
 *
 * @return mixed PlugIn
 */
function rePlug($name, $object)
{
    $objs = getOption(PLUGIN_INSTANCE);
    $objs[$name] = $object;

    return $objs[$name];
}

/**
 * Get PlugIn Names.
 *
 * @return mixed
 */
function getPlugs()
{
    $objs = getOption(PLUGIN_INSTANCE);

    return $objs->keyset();
}

/**
 * Init PlugIn.
 *
 * @param array $arr   plug-in array
 * @param bool  $pause for includ file only
 *
 * @return void
 */
function initPlugIn(array $arr, $pause = false)
{
    $init = [];
    $objs = getOption(PLUGIN_INSTANCE);
    foreach ($arr as $plugIn => $config) {
        if (!isset($objs[$plugIn])) {
            if ($pause) {
                $config[PAUSE] = true;
            }
            if (empty($config)) {
                plug($plugIn);
            } else {
                $init[$plugIn] = plug($plugIn, $config);
            }
        }
    }

    return $init;
}

/**
 * Plug.
 *
 * @param string $name   plugin name
 * @param array  $config plugin configs
 *
 * @return mixed
 */
function plug($name, array $config = [])
{
    $objs = getOption(PLUGIN_INSTANCE);
    if (isset($objs[$name])) {
        $oPlugin = $objs[$name];
        if (!empty($config)) {
            set($oPlugin, $config);
        }

        return $oPlugin->update();
    }
    $folders = folders(_PLUGIN);
    $alias = $folders['alias'];
    if (isset($alias[$name])) {
        $objs[$name] = plug($alias[$name]);

        return $objs[$name];
    }
    if (isset($config[_CLASS]) && class_exists($config[_CLASS])) {
        $class = $config[_CLASS];
    } else {
        $file = null;
        if (isset($config[_PLUGIN_FILE])) {
            $file = $config[_PLUGIN_FILE];
        }
        if (realpath($file)) {
            $r = l($file, _INIT_CONFIG);
        } else {
            $file = $name.'/'.$name.'.php';
            $r = load($file, $folders['folders'], _INIT_CONFIG, true, false);
        }
        $class = (!empty($r->var[_INIT_CONFIG][_CLASS]))
            ? $r->var[_INIT_CONFIG][_CLASS]
            : false;
    }
    $exists = class_exists($class);
    if (!empty($config[PAUSE])) {
        return $exists; //for inclue only purpose
    }
    if ($exists) {
        $oPlugin = new $class();
    } else {
        if (!$class) {
            $error = 'plugin '.$name.' not found.';
            if (!empty($file)) {
                $error .= ' ['.$file.']';
            }
        } else {
            $error = 'plugIn '.$name.': class not found ('.$class.')';
        }

        return !trigger_error($error);
    }
    $oPlugin[_PLUGIN] = $name;
    $oPlugin['this'] = new Adapter($name);
    if (!empty($r)) {
        $config = arrayReplace($r->var[_INIT_CONFIG], $config);
        $oPlugin[_PLUGIN_FILE] = $r->name;
    }
    if (!empty($config)) {
        set($oPlugin, $config);
    }
    $objs[$name] = $oPlugin;
    $oPlugin->init();

    return $oPlugin->update();
}
