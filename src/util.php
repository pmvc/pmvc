<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category Core
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
use DomainException;
use OverflowException;
use stdClass;

option(
    'set',
    [
        ERRORS => new HashMap(),
    ]
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
 * @param string $name   File name
 * @param string $export Extract one variable name.
 * @param bool   $once   If incldue once
 *
 * @return mixed
 */
function l($name, $export = null, $once = true)
{
    $real = realpath($name);
    if (!$real) {
        return !trigger_error('File not found. ['.$name.']');
    }
    if ($once) {
        return run(__NAMESPACE__.'\_l', [$real, $export]);
    } else {
        return _l($real, $export);
    }
}

/**
 * Private funciton for l.
 *
 * @param string $name   file name
 * @param string $export Extract one variable name.
 *
 * @return mixed
 */
function _l($name, $export = null)
{
    include $name;
    $o = new stdClass();
    $o->name = $name;
    if ($export) {
        $o->var = compact($export);
    }

    return $o;
}

/**
 * Smart Load.
 *
 * @param string $name   name
 * @param mixed  $dirs   dirs
 * @param string $output Extract one variable
 * @param bool   $once   if incldue once
 *
 * @return mixed
 */
function load(
    $name,
    $dirs = null,
    $output = null,
    $once = true
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
        ]
    );
    if ($file) {
        return l($file, $output, $once);
    } else {
        return 2;
    }
}

/**
 * Smart find.
 *
 * @param string $name name
 * @param mixed  $dirs dirs
 *
 * @return mixed
 */
function find($name, $dirs = null)
{
    $dirs = splitDir($dirs);
    foreach ($dirs as $dirPath) {
        if (!realpath($dirPath)) {
            continue;
        }
        $r = realpath(mergeFileName($name, $dirPath));
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
    if (!empty($folders)) {
        $folders = array_map(
            function ($f) {
                return realpath($f);
            },
            $folders
        );
        $folders = array_filter($folders);
        if (!empty($folders)) {
            $_folders[$type] = array_unique(
                array_merge(
                    $_folders[$type],
                    $folders
                )
            );
        }
    }
    $_alias[$type] = array_merge(
        $_alias[$type],
        $alias
    );

    return [
       'folders' => array_reverse($_folders[$type]),
       'alias'   => $_alias[$type],
    ];
}

/*
 * File -->.
 */

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
    $c = strlen($s);
    if ($c && $s[$c - 1] !== '/') {
        $s .= '/';
    }

    return $s;
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

    return md5(serialize($params));
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
 * Test is a valid string contain number.
 *
 * @param mixed $s Test string
 *
 * @return bool
 */
function testString($s)
{
    return is_string($s) || is_numeric($s) || is_null($s) || is_bool($s);
}

/*
 * String Util -->.
 */

/**
 * Array Util <!---.
 */

/**
 * Check an array or string equal one value.
 *
 * @param mixed  $haystack search on array or string
 * @param string $needle   search keyword
 *
 * @return bool
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
        return !trigger_error(
            'Param1 should be array type. '."\n".
            print_r([$new, isArrayAccess($new) ? 'true' : 'false'], true)
        );
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
 * @param mixed $p          parameters
 * @param bool  $onlyValues Return all the values of an array
 *
 * @return array
 */
function toArray($p, $onlyValues = false)
{
    if (is_null($p)) {
        $p = [];
    } elseif (is_object($p)) {
        if (isArrayAccess($p)) {
            $p = get($p);
        } else {
            $p = (array) $p;
        }
    } elseif (!is_array($p)) {
        $p = [$p];
    }
    if ($onlyValues) {
        $p = array_values($p);
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
    return is_array($obj) || isArrayAccess($obj);
}

/**
 * Safe get multi layer array value.
 *
 * @param array $a       array
 * @param array $path    array's path
 * @param mixed $default if value not exists, return default value
 *
 * @return mixed
 */
function value($a, array $path, $default = null)
{
    foreach ($path as $p) {
        $a = &get($a, $p);
        if (is_null($a)) {
            if (is_callable($default)) {
                $default = call_user_func($default);
            }

            return $default;
        }
    }

    return $a;
}

/**
 * Get reference value.
 *
 * @param mixed $v   variable
 * @param mixed $new new value
 *
 * @return mixed
 */
function &ref(&$v, $new = null)
{
    if (is_a($v, __NAMESPACE__.'\BaseObject')) {
        return $v($new);
    } else {
        if (!is_null($new)) {
            $v = $new;
        }

        return $v;
    }
}

/*
 * Array Util -->.
 */

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
    if (isArrayAccess($a)) {
        $a->offsetUnset($k);

        return;
    }
    if (is_null($k)) { //clean all
        $a = [];
        unset($a);

        return;
    } else {
        if (isArray($k)) { //replace
            $a = $k;

            return;
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
    if (isArrayAccess($a)) {
        /**
         * Can't assign default by $a[$k]
         * So the default value will handle in last.
         */
        $v = &$a->offsetGet($k);
        if (!is_null($v)) {
            return $v;
        }
    }
    if (is_null($k)) { //return all
        return $a;
    } elseif (is_array($k)) { //return by keys
        $r = [];
        if (is_array($a)) {
            foreach ($k as $i) {
                if (!testString($i)) {
                    continue;
                }
                if (isset($a[$i])) {
                    $r[$i] = &$a[$i];
                }
            }
        } elseif (is_object($a)) {
            foreach ($k as $i) {
                if (!testString($i)) {
                    continue;
                }
                if (isset($a->{$i})) {
                    $r[$i] = &$a->{$i};
                }
            }
        }

        return $r;
    } else {
        //return one
        if (testString($k)) {
            if (is_array($a) && isset($a[$k])) {
                return $a[$k];
            } elseif (is_object($a) && isset($a->{$k})) {
                return $a->{$k};
            }
        }
    }
    if (is_callable($default)) {
        $default = call_user_func($default);
    }

    return $default;
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
        if (!isArray($k)) {
            $k = (array) $k;
        }

        return $a = arrayReplace($a, $k); //merge by new array
    } elseif (is_null($k)) {
        return $a[] = $v; //append value when no-assign key
    } else {
        return $a[$k] = $v; //exactly set key and value
    }
}

/*
 * Data access -->.
 */

/**
 * Option <!---.
 */

/**
 * Get Option.
 *
 * @param mixed $k       which want to get
 * @param mixed $default value or default
 *
 * @return mixed
 */
function &getOption($k = null, $default = null)
{
    return option('get', $k, $default);
}

/**
 * Global option for get/set.
 *
 * Keep in mind why don't have clean and isset
 * 1. Make function more simple
 *    for get better performance.
 * 2. Get always have value, don't need isset
 * 3. Clean is not useful here,
 *    some value need always keep.
 * 4. Mose of use case is append not replace all.
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

/*
 * Option -->.
 */

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
 * Variable dump.
 *
 * @return void
 */
function v()
{
    $p = func_get_args();
    if (1 === count($p)) {
        $p = $p[0];
    } else {
        // avoid console.table
        $p[''] = $p[0];
        unset($p[0]);
    }
    d(json_encode($p));
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
 * @return mixed
 */
function dev()
{
    return callPlugin('dev', 'dump', func_get_args());
}

/**
 * Is develop.
 *
 * @return bool
 */
function isDev()
{
    $result = callPlugin('dev', 'isDev', func_get_args());

    return !empty($result);
}

/**
 * Cache function run result.
 *
 * @param function $func run function
 * @param array    $args parameters
 *
 * @return mixed Cache result.
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
 * @param mixed  $v    A mixed type to check exists.
 * @param string $type Check type.
 *
 * @return bool
 */
function exists($v, $type)
{
    if (!strlen($v)) {
        return false;
    }
    switch (strtolower($type)) {
    case 'plugin':
        return (bool) plugInStore($v);
    case 'plug': //check if OK to plug
        if (plugInStore($v)) {
            return true;
        } else {
            return plug($v, [PAUSE => true]);
        }
    default:
        throw new DomainException(
            'Exists checker not support ['.
            $type.
            ']'
        );
    }
}

/*
 * Misc -->.
 */

/**
 * Plugins <!---.
 */

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
    dev(
        /**
         * Dev.
         *
         * @help Debug for PMVC add plugin folder.
         */
        function () use ($folders, $alias) {
            $trace = plug('debug')->parseTrace(debug_backtrace(), 9);

            return [
                'previous' => folders(_PLUGIN),
                'folders'  => $folders,
                'alias'    => $alias,
                'trace'    => $trace,
            ];
        },
        'plugin-folder'
    );

    return folders(_PLUGIN, $folders, $alias);
}

/**
 * PlugIn Store for Security.
 *
 * @param string $key        plug-in name
 * @param PlugIn $value      [null: get only] [false: unset] [other: set]
 * @param bool   $isSecurity security flag
 *
 * @return mixed
 */
function plugInStore($key = null, $value = null, $isSecurity = false)
{
    static $plugins = [];
    static $securitys = [];
    $currentPlug = false;
    if (isset($plugins[$key])) {
        $currentPlug = $plugins[$key];
    }
    if ($isSecurity) {
        if ($currentPlug) {
            throw new OverflowException(
                'Security plugin ['.$key.'] already plug, '.
                'you need check your code if it is safe.'
            );
        }
        $securitys[$key] = true;
    }
    if (is_null($value)) {
        if (!is_null($key)) {
            return $currentPlug;
        } else {
            return array_keys($plugins);
        }
    }
    if (isset($securitys[$key]) && $currentPlug) {
        return !trigger_error('You can not change security plugin. ['.$key.']');
    }
    if (empty($value)) {
        if ($currentPlug) {
            $plugins[$key] = null;
            unset($plugins[$key]);
        }
    } else {
        $plugins[$key] = $value;
    }

    return $currentPlug;
}

/**
 * Call Plug-In.
 *
 * @param string $plugIn plug-in name
 * @param string $func   plug-in function name
 * @param array  $args   plug-in function parameters
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
 * @return PlugIn
 */
function unPlug($name)
{
    return plugInStore($name, false);
}

/**
 * Re plug.
 *
 * @param sring  $name   plug-in name
 * @param PlugIn $object plug-in plugin instance
 *
 * @return PlugIn
 */
function rePlug($name, $object)
{
    $object[NAME] = $name;
    $object[THIS] = new Adapter($name);

    return plugInStore($name, $object, $object[_IS_SECURITY]);
}

/**
 * Init PlugIn.
 *
 * @param array $arr   plug-in array
 * @param bool  $pause for includ file only
 *
 * @return array
 */
function initPlugIn(array $arr, $pause = false)
{
    $init = [];
    foreach ($arr as $plugIn => $config) {
        $isPlug = plugInStore($plugIn);
        if (empty($isPlug) || !empty($config)) {
            if (empty($config)) {
                $config = [];
            }
            if ($pause) {
                $config[PAUSE] = true;
            }
            $init[$plugIn] = plug($plugIn, $config);
        }
    }

    return $init;
}

/**
 * Plug alias.
 *
 * @param string $targetPlugin Target plugin.
 * @param string $aliasName    New alias name.
 *
 * @return PlugIn
 */
function plugAlias($targetPlugin, $aliasName)
{
    $oPlugin = plugInStore($targetPlugin);
    if (empty($oPlugin)) {
        throw new DomainException(
            'Plug alias fail. Target: ['.
            $targetPlugin.
            '], New Alias: ['.
            $aliasName.
            ']'
        );
    }
    plugInStore($aliasName, $oPlugin);

    return $oPlugin;
}

/**
 * Plug Config.
 *
 * @param PlugIn $oPlugin Plug-in object
 * @param array  $config  Plug-in configs
 *
 * @return void
 */
function plugConfig($oPlugin, array $config)
{
    if (!empty($oPlugin) && !empty($config)) {
        if (is_callable(get($config, _LAZY_CONFIG))) {
            $config = array_replace(
                $config,
                $config[_LAZY_CONFIG]()
            );
        }
        set($oPlugin, $config);
    }
}

/**
 * Plug.
 *
 * @param string $name   Plug-in name
 * @param array  $config Plug-in configs
 *
 * @return PlugIn
 */
function plug($name, array $config = [])
{
    if (!is_string($name)) {
        return !trigger_error('Plug name should be string. '.print_r($name, true));
    }
    if (empty($config)) {
        $oPlugin = plugInStore($name);
    } else {
        $oPlugin = plugInStore($name, null, get($config, _IS_SECURITY));
        plugConfig($oPlugin, $config);
    }
    if (!empty($oPlugin)) {
        return $oPlugin->update();
    }
    $names = explode('_', $name);
    $config = array_replace(
        value(
            getOption('PLUGIN'),
            $names,
            []
        ),
        value(
            getOption('PW'),
            $names,
            []
        ),
        $config
    );
    $folders = folders(_PLUGIN);
    $alias = $folders['alias'];
    if (isset($alias[$name])) {
        return plugAlias($alias[$name], $name);
    }
    if (isset($config[_CLASS]) && class_exists($config[_CLASS])) {
        $class = $config[_CLASS];
    } else {
        $file = null;
        if (isset($config[_PLUGIN_FILE])) {
            $file = realpath($config[_PLUGIN_FILE]);
            if (empty($file)) {
                return !trigger_error(
                    'PlugIn '.$name.': defined file not found. '.
                    '['.$config[_PLUGIN_FILE].']'
                );
            }
            // assign realpath
            $config[_PLUGIN_FILE] = $file;
        }
        if ($file) {
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
        if (!($oPlugin instanceof PlugIn)) {
            return !trigger_error('Class is not a plug-in(\PMVC\PlugIn) instance.');
        }
    } else {
        if (!$class) {
            $error = 'Plug-in '.$name.' not found.';
            if (!empty($file)) {
                $error .= ' ['.$file.'] '.print_r($folders['folders'], true);
            }
        } else {
            $error = 'Plug-in '.$name.': class not found ('.$class.')';
        }

        return !trigger_error($error);
    }
    if (!empty($r)) {
        $config = arrayReplace($r->var[_INIT_CONFIG], $config);
        $config[_PLUGIN_FILE] = $r->name;
    }
    plugConfig($oPlugin, $config);
    rePlug($name, $oPlugin);
    $oPlugin->init();
    dev(
        /**
         * Dev.
         *
         * @help Debug for PMVC plug.
         */
        function () use ($name) {
            $trace = plug('debug')->parseTrace(debug_backtrace(), 9);

            return [
                'name'  => $name,
                'trace' => $trace,
            ];
        }, 'plug'
    );

    return $oPlugin->update();
}

/*
 * Plugins -->.
 */
