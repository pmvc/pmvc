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
    static $files = [];
    $real = realpath($name);
    if (!$once || !isset($files[$real])) {
        include $name;
        $files[$real] = false;
    }
    $o = new \stdClass();
    $o->name = $real;
    if ($compacts) {
        if (!empty($files[$real])) {
            $o->var = &$files[$real];
        } else {
            $o->var = compact($compacts);
            $files[$real] = &$o->var;
        }
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
        if ($once) {
            $r = run(__NAMESPACE__.'\l', [$file, $compacts]);
        } else {
            $r = l($file, $compacts, $once);
        }

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
        $r = includeApp(mergeName($name, $dirPath), $isIncludeApp);
        if ($r) {
            return $r;
        }
    }

    return false;
}

/**
 * String Util (Path or Folder parse) <!---.
 */

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
 * Change file name from uppder case to lower case.
 *
 * @param string $name file name
 * @param string $type [class|function]
 *
 * @return string
 */
function lowerCaseFile($name, $type = '')
{
    $s = preg_split(
        '/([A-Z])/',
        $name,
        -1,
        PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
    );
    $k = '';
    for ($i = 1, $j = count($s); $i < $j; $i++) {
        if (preg_match('/[A-Z]/', $s[$i])) {
            $k .= '_'.strtolower($s[$i]);
        } else {
            $k .= $s[$i];
        }
    }
    if (!empty($type)) {
        $type .= '.';
    }
    $k = $type.strtolower($s[0]).$k;

    return $k;
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
 * Merge name.
 *
 * @param string $name name
 * @param string $dir  dir
 *
 * @return string
 */
function mergeName($name, $dir = null)
{
    if (!empty($dir)) {
        $name = lastSlash($dir).$name;
    }

    return $name;
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
function Array_merge()
{
    $a = func_get_args();
    $new = $a[0];
    if (!isArray($new)) {
        return !trigger_error('param1 need be an array');
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
        return $a = array_merge($a, $k);
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
    call_plugin('debug', 'd', $params);
}

/**
 * Log for debug.
 *
 * @return vod
 */
function log()
{
    $params = func_get_args();
    call_plugin('error_trace', 'log', $params);
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
 * Count number.
 *
 * @param mixed $v    value
 * @param mixed $type [array|string]
 *
 * @return mixed
 */
function n($v, $type = null)
{
    if (is_array($v)) {
        if (!is_null($type) && 'array' != $type) {
            return false;
        }

        return count($v);
    } else {
        if (!is_string($v)) {
            return false;
        }

        return strlen($v);
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
    option('set', PLUGIN_ALIAS, $alias);

    return option('set', PLUGIN_FOLDERS, toArray($folders));
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
    $folders = \array_merge(
        toArray(getOption(PLUGIN_FOLDERS)),
        toArray($folders)
    );
    $alias = \array_merge(
        toArray(getOption(PLUGIN_ALIAS)),
        $alias
    );
    setPlugInFolder($folders, $alias);
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
function Call_plugIn($plugIn, $func, $args = [])
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
 * @return mixed \PMVC\PlugIn
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
 * @return mixed \PMVC\PlugIn
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
 * @param array $arr plug-in array
 *
 * @return void
 */
function initPlugIn($arr)
{
    if (is_array($arr)) {
        $objs = getOption(PLUGIN_INSTANCE);
        foreach ($arr as $plugIn => $config) {
            if (!isset($objs[$plugIn])) {
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
        if (!isset($config[_PLUGIN_FILE])) {
            $alias = getOption(PLUGIN_ALIAS);
            if (isset($alias[$name])) {
                $file = $alias[$name];
            }
        } else {
            $file = $config[_PLUGIN_FILE];
        }
        if (!is_null($file) && realpath($file)) {
            $r = run(__NAMESPACE__.'\l', [$file, _INIT_CONFIG]);
        } else {
            $file = $name.'.php';
            $default_folders = getOption(PLUGIN_FOLDERS, []);
            $folders = [];
            foreach ($default_folders as $folder) {
                $folders[] = lastSlash($folder).$name;
            }
            $r = load($file, $folders, _INIT_CONFIG, true, false);
        }
        $class = (!empty($r->var[_INIT_CONFIG][_CLASS]))
            ? $r->var[_INIT_CONFIG][_CLASS]
            : false;
    }
    if (!empty($config[\PMVC\PAUSE])) {
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
        $config = array_merge($r->var[_INIT_CONFIG], $config);
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
