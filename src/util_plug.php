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

/**
 * File <--------------.
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
    $real = realPath($name);
    if ($once) {
        return run(__NAMESPACE__.'\_l', [$real, $compacts]);
    } else {
        return _l($real, $compacts);
    }
}

/**
 * Private funciton for l
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
 * Include app folder.
 *
 * @param string $name         file name
 * @param string $bTransparent Transparent app folder
 *
 * @return mixed
 */
function includeApp($name, $bTransparent = null)
{
    if (!$bTransparent) {
        return realpath($name);
    }
    $transparent = run(__NAMESPACE__.'\transparent', [$name]);
    $transparent = realpath($transparent);
    if ($transparent) {
        return $transparent;
    } else {
        return realpath($name);
    }
}

/**
 * Smart Load.
 *
 * @param string $name         name
 * @param mixed  $dirs         dirs
 * @param mixed  $compacts     decide extrac files variable
 * @param bool   $once         if incldue once
 * @param bool   $isIncludeApp search for application folder
 *
 * @return mixed
 */
function load(
    $name,
    $dirs = null,
    $compacts = null,
    $once = true,
    $isIncludeApp = null
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
            $isIncludeApp,
        ]
    );
    if ($file) {
        $r = l($file, $compacts, $once);

        return $r;
    } else {
        return 2;
    }
}

/**
 * Smart find.
 *
 * @param string $name         name
 * @param mixed  $dirs         dirs
 * @param bool   $isIncludeApp search for application folder
 *
 * @return mixed
 */
function find($name, $dirs = null, $isIncludeApp = null)
{
    $dirs = splitDir($dirs);
    foreach ($dirs as $dirPath) {
        if (!realpath($dirPath)) {
            continue;
        }
        $r = includeApp(mergeFileName($name, $dirPath), $isIncludeApp);
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
function folders($type, $folders = [], $alias = [], $clean = null)
{
    static $_folders = [];
    static $_alias = [];
    if (!isset($_folders[$type]) || $clean) {
        $_folders[$type] = [];
        $_alias[$type] = [];
    }
    $_folders[$type] = \array_merge(
        $_folders[$type],
        toArray($folders)
    );
    $_alias[$type] = \array_merge(
        $_alias[$type],
        $alias 
    );

    return [
       'folders' => array_reverse($_folders[$type]),
       'alias' => $_alias[$type]
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
function isContain($haystack, $needle)
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
 * Array Merge (The numeric key will be overwrite not append).
 *
 * @return array
 */
function arrayMerge()
{
    $a = func_get_args();
    $new = $a[0];
    if (!isArray($new)) {
        return !trigger_error('Param1 need be an array. '.var_export($new, true));
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
 * Merge array with a default set
 * If key not in default set will be ignore.
 *
 * @param array $defaults default
 * @param array $settings setting
 *
 * @return array
 */
function mergeDefault($defaults, $settings)
{
    foreach ($defaults as $k => $v) {
        if (isset($settings[$k])) {
            $defaults[$k] = $settings[$k];
        }
    }

    return $defaults;
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
    return is_a($obj, 'ArrayAccess');
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
    return isArrayAccess($obj) || is_array($obj);
}

/**
 * Safe get multi layer array value.
 *
 * @param mixed $arr     array
 * @param mixed $path    array's path
 * @param mixed $default if value not exists, return default value
 *
 * @return bool
 */
function value($arr, $path, $default = null)
{
    if (!isArray($arr)) {
        return !trigger_error('Target is not array');
    }
    if (!isArray($path)) {
        return !trigger_error('Path is not array');
    }
    $a = &$arr;
    foreach ($path as $p) {
        if (isset($a[$p])) {
            $a = &$a[$p];
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
            $a = null;
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
            return $a->offsetGet();
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
    if (isArray($k)) { //merge by new array
        return $a = arrayMerge($a, $k);
    } else {
        if (is_null($k) && is_null($v)) {
            return false;
        } elseif (is_null($k)) { //append value when no-assign key
            return $a[] = $v;
        } elseif (!is_string($k)) { //append key when key is not a string
            return $a[] = $k;
        } else { //exactly set key and value
            return $a[$k] = $v;
        }
    }
}

/**
 * Option <!-----------.
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
 * Misc <!----------.
 */

/**
 * Dump for debug.
 *
 * @return vod
 */
function d()
{
    $params = func_get_args();
    callPlugin('debug', 'd', $params);
}

/**
 * Log for debug.
 *
 * @return vod
 */
function log()
{
    $params = func_get_args();
    callPlugin('error_trace', 'log', $params);
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
 * Plugins <!--.
 */

/**
 * Get Adapter.
 *
 * @param string $name Adapter name
 *
 * @return Adapter instance
 */
function getAdapter($name)
{
    static $adapters = [];
    if (!isset($adapters[$name])) {
        $adapters[$name] = new Adapter($name);
    }

    return $adapters[$name];
}

/**
 * Set PlugIn Folder.
 *
 * @param array $folders plug-in folders
 * @param array $alias   plug-in alias
 *
 * @return mixed
 */
function setPlugInFolder($folders, $alias = [])
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
function addPlugInFolder($folders, $alias = [])
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
function initPlugIn($arr, $pause = false)
{
    if (is_array($arr)) {
        $objs = getOption(PLUGIN_INSTANCE);
        foreach ($arr as $plugIn => $config) {
            if (!isset($objs[$plugIn])) {
                if ($pause) {
                    $config[PAUSE] = true;
                }
                plug($plugIn, $config);
            }
        }
    }
}

/**
 * Plug.
 *
 * @param string $name   plugin name
 * @param array  $config plugin configs
 *
 * @return mixed
 */
function plug($name, $config = null)
{
    $objs = getOption(PLUGIN_INSTANCE);
    if (isset($objs[$name])) {
        $oPlugin = $objs[$name];
        if (!is_null($config)) {
            set($oPlugin, $config);
        }

        return $oPlugin->update();
    }
    if (isset($config[_CLASS]) && class_exists($config[_CLASS])) {
        $class = $config[_CLASS];
    } else {
        $file = null;
        $folders = folders(_PLUGIN);
        if (!isset($config[_PLUGIN_FILE])) {
            $alias = $folders['alias'];
            if (isset($alias[$name])) {
                $file = $alias[$name];
            }
        } else {
            $file = $config[_PLUGIN_FILE];
        }
        if (!is_null($file) && realpath($file)) {
            $r = run(__NAMESPACE__.'\l', [$file, _INIT_CONFIG]);
        } else {
            $file = $name.'/'.$name.'.php';
            $r = load($file, $folders['folders'], _INIT_CONFIG, true, false);
        }
        $class = (!empty($r->var[_INIT_CONFIG][_CLASS]))
            ? $r->var[_INIT_CONFIG][_CLASS]
            : false;
    }
    if (!empty($config[PAUSE])) {
        return; //for inclue only purpose
    }
    if (class_exists($class)) {
        $oPlugin = new $class();
    } else {
        if (!$class) {
            $error = 'plugin '.$name.' not found';
        } else {
            $error = 'plugIn '.$name.': class not found ('.$class.')';
        }
        trigger_error($error);
        $name = false;

        return $name;
    }
    $oPlugin[_PLUGIN] = $name;
    $oPlugin['this'] = getAdapter($name);
    if (!empty($r)) {
        $config = arrayMerge($r->var[_INIT_CONFIG], $config);
        $oPlugin[_PLUGIN_FILE] = $r->name;
    }
    if (!empty($config)) {
        set($oPlugin, $config);
    }
    if (is_null($objs)) {
        option(
            'set',
            PLUGIN_INSTANCE,
            new HashMap([$name => $oPlugin])
        );
    } else {
        $objs[$name] = $oPlugin;
    }
    $oPlugin->init();

    return $oPlugin->update();
}
