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

/**
 * PMVC Loader.
 *
 * @category Core
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class Load
{
    /**
     * Include plugin only.
     *
     * @param mixed $init    Default plugins or lazy function
     * @param array $folders Extra plugin folder
     * @param array $options PMVC options
     *
     * @return bool
     */
    public static function plug(
        $init = [],
        array $folders = [],
        array $options = []
    ) {
        if (is_callable($init)) {
            $params = $init();
            $init = $params[0];
            $folders = $params[1];
            $options = $params[2];
        }
        $options[ERRORS] = new HashMap();
        option('set', $options);
        if (!empty($folders)) {
            addPlugInFolders($folders);
        }
        self::initPlugInFolder();
        if (!empty($init)) {
            initPlugin($init, get($options, PAUSE));
        }
    }

    /**
     * Init plug folder.
     *
     * @return bool
     */
    public static function initPlugInFolder()
    {
        addPlugInFolders([__DIR__.'/../../../pmvc-plugin']);
    }
}

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
 * Same with include, but manage include_once by self.
 * and make global variable to local variable.
 *
 * @param string $name    File name
 * @param string $export  Extract one variable name.
 * @param bool   $options $once, $ignore, $import
 *
 * @return mixed
 */
function l($name, $export = null, $options = [])
{
    $once = get($options, 'once', true);
    $real = realpath($name.'.php');
    if (!$real) {
        $real = realpath($name);
        if (!$real) {
            $ignore = get($options, 'ignore');
            if ($ignore) {
                return false;
            } else {
                return !trigger_error('File not found. ['.$name.']');
            }
        }
    }
    $import = get($options, 'import');
    if ($once) {
        return run(
            ns('InternalUtility::l'),
            [$real, $export, $import]
        );
    } else {
        return InternalUtility::l($real, $export, $import);
    }
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
function load($name, $dirs = null, $output = null, $once = true)
{
    if (empty($name)) {
        return 1;
    }
    /*
     * Cache find in load case, if some case can't use cahce please use find directly
     */
    $file = run(ns('find'), [$name, $dirs]);
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
        $mergeName = mergeFileName($name, $dirPath);
        if (realpath($mergeName)) {
            return $mergeName;
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
                array_merge($_folders[$type], $folders)
            );
        }
    }
    foreach ($alias as $k => $v) {
        $_alias[$type][strtolower($k)] = $v;
    }

    return [
        'folders' => array_reverse($_folders[$type]),
        'alias'   => $_alias[$type],
    ];
}

/*
 * File -->.
 */

/**
 * String (Path or Folder parse) <!---.
 */

/**
 * UTF8 Export.
 *
 * @param mixed    $p      Payload.
 * @param callable $exists Let exists func mockable.
 *
 * @return mixed
 */
function utf8Export($p, $exists = '\PMVC\exists')
{
    return $exists('utf8', 'plug')
        ? plug('utf8')->toUtf8($p)
        : (testString($p)
            ? utf8_encode($p)
            : $p);
}

/**
 * UTF8 Json Encode.
 *
 * @param mixed $p     payload.
 * @param int   $flags flags.
 *
 * @return mixed
 */
function utf8JsonEncode($p, $flags = 0)
{
    if (!$flags && defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        $flags = JSON_INVALID_UTF8_SUBSTITUTE;
    }

    return json_encode(utf8Export($p), $flags);
}

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
        return toArray($s);
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

/**
 * Transform camelcase.
 *
 * @param string $s    Input with camel case.
 * @param string $join Delimiter.
 *
 * @return mixed
 */
function camelCase($s, $join = null)
{
    if (empty($s)) {
        return $s;
    }
    $arr = preg_split(
        '/([A-Z][^A-Z]*)/',
        $s,
        -1,
        PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
    );
    if (is_null($join)) {
        $result = array_map('strtolower', $arr);
    } else {
        $result = strtolower($arr[0]);
        for ($i = 1, $j = count($arr); $i < $j; $i++) {
            $result .= $join.strtolower($arr[$i]);
        }
    }

    return $result;
}

/*
 * String -->.
 */

/**
 * Array <!---.
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
    if ($haystack === $needle || isset($haystack[$needle])) {
        return true;
    } else {
        return false;
    }
}

/**
 * Array Replace (The numeric key will be overwrite not append).
 * Different with array_replace, it accept ArrayAccess object.
 *
 * @return array
 */
function arrayReplace()
{
    $a = func_get_args();
    // Do not make this reference,
    // it helpful if you just pass pure array without assign.
    $new = $a[0];
    if (!isArray($new)) {
        return triggerJson('Param1 should be array type.', ['Array' => $new]);
    }
    for ($i = 1, $j = count($a); $i < $j; $i++) {
        // This is for handle empty array not empty value
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
        $p = get($p);
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
 * @param mixed $setter  set values.
 *
 * @return mixed
 */
function value(&$a, array $path, $default = null, $setter = null)
{
    $getValue = function ($v) {
        if (is_callable($v)) {
            $v = call_user_func($v);
        }

        return $v;
    };
    $setValue = function (&$p, $k, $v) use ($getValue) {
        $v = $getValue($v);
        if (is_object($p)) {
            $p->{$k} = $v;
        } else {
            set($p, $k, $v);
        }

        return $v;
    };
    if (!is_null($setter)) {
        $lastPath = array_pop($path);
        if (is_null($default)) {
            $default = [];
        }
    } else {
        $lastPath = null;
    }
    $previous = &$a;
    foreach ($path as $p) {
        unset($next);
        $next = &get($previous, $p);
        if (is_null($next)) {
            $defV = $getValue($default);
            if ($lastPath) {
                $setValue($previous, $p, $defV);
                $next = &get($previous, $p);
            } else {
                return $defV;
            }
        }
        unset($previous);
        $previous = &$next;
    }

    if (!isset($next)) {
        $next = &$a;
    }

    return $lastPath ? $setValue($previous, $lastPath, $setter) : $next;
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
    if (is_a($v, ns('BaseObject'))) {
        return $v($new);
    } else {
        if (!is_null($new)) {
            $v = $new;
        }

        return $v;
    }
}

/**
 * Pass by reference.
 *
 * @param mixed $v variable
 *
 * @return mixed
 */
function &passByRef($v)
{
    return $v;
}

/*
 * Array -->.
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

        return $a;
    }
    if (is_null($k)) {
        //clean all
        $a = [];

        return $a;
    } else {
        if (isArray($k)) {
            //replace
            $a = $k;

            return $a;
        } else {
            unset($a[$k]); //clean by key

            return $a;
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
    /**
     * Can't assign default by $a[$k]
     * So the default value will handle at last.
     */
    if (isArrayAccess($a)) {
        if (is_null($k) && method_exists($a, 'toArray')) {
            $v = $a->toArray();
        } else {
            $v = $a->offsetGet($k);
        }
        if (!is_null($v)) {
            if (is_null($k) && is_array($v)) {
                foreach ($v as $vk => $vv) {
                    if (isArrayAccess($vv)) {
                        $v[$vk] = $vv->offsetGet();
                    }
                }
            }

            return $v;
        }
    }
    if (is_null($k) || false === $k) {
        //return all
        if (is_object($a)) {
            $r = get_object_vars($a);
            $r = $r ? $r : (array) $a;

            return $r;
        }

        return $a;
    } elseif (is_array($k)) {
        //return by keys
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
                if (!is_string($i)) {
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
 * Transpile namespace string.
 * It will always be PMVC\xxx,
 * so it only could use with pmvc.
 *
 * @param string $s class string or function string
 *
 * @return namestpace string
 */
function ns($s)
{
    return __NAMESPACE__.'\\'.$s;
}

/**
 * Trigger error with json format.
 *
 * @param string $error Error message
 * @param object $debug Error payload
 * @param int    $type  Error type
 *
 * @return false
 */
function triggerJson($error, $debug = null, $type = E_USER_NOTICE)
{
    return !trigger_error(
        utf8JsonEncode(['Error' => $error, 'Debug' => $debug]),
        $type
    );
}

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
        foreach ($p as &$pn) {
            $pn = get($pn);
        }
    }
    d(testString($p) ? $p : utf8JsonEncode($p));
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
        return InternalUtility::isPlugInExists($v);
    case 'plug': //check if OK to plug
        if (InternalUtility::isPlugInExists($v)) {
            return true;
        } else {
            return InternalUtility::initPlugInObject(
                $v,
                passByRef([PAUSE => true]),
                folders(_PLUGIN)['folders']
            );
        }
    default:
        throw new DomainException(
            'Exists checker not support ['.$type.']'
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
 * Call Plug-In.
 *
 * @param string $plugIn plug-in name
 * @param string $func   plug-in function name
 * @param array  $args   plug-in function parameters
 *
 * @return mixed
 */
function callPlugin($plugIn, $func = null, $args = [])
{
    if (exists($plugIn, 'plugin')) {
        return is_null($func)
            ? plug($plugIn)
            : call_user_func_array([plug($plugIn), $func], $args);
    }
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
 * Unplug.
 *
 * @param sring $name   Plug-in name.
 * @param sring $reject Reject replug again.
 *
 * @return PlugIn
 */
function unPlug($name, $reject = false)
{
    return (bool) InternalUtility::plugInStore($name, false, $reject);
}

/**
 * Re plug.
 *
 * @param sring  $name   plug-in name
 * @param array  $config Plug-in configs
 * @param PlugIn $object plug-in plugin instance
 *
 * @return PlugIn
 */
function rePlug($name, array $config = [], $object = null)
{
    if (!is_null($object)) {
        $cookName = strtolower($name);
        $config[NAME] = $cookName;
        $config[THIS] = new Adapter($cookName);
        if (isArray($object)) {
            InternalUtility::setPlugInConfig($object, $config);
        }

        InternalUtility::plugInStore(
            $name,
            $object,
            get($object, _IS_SECURITY)
        );

        return $config[THIS];
    } else {
        unplug($name);

        return plug($name, $config);
    }
}

/**
 * Init PlugIn.
 *
 * @param array $arr   plug-in array
 * @param bool  $pause for includ file only
 *
 * @return array set pause to true will return plugin exists array
 */
function initPlugIn(array $arr, $pause = false)
{
    $init = [];
    $plugInFolders = folders(_PLUGIN)['folders'];
    foreach ($arr as $plugIn => $config) {
        if (!exists($plugIn, 'plugin') || !empty($config)) {
            if ($pause || false === $config) {
                $config = $config ? $config : [];
                $config[PAUSE] = true;
                $init[$plugIn] = InternalUtility::initPlugInObject(
                    $plugIn,
                    $config,
                    $plugInFolders
                );
            } else {
                $init[$plugIn] = $config ? plug($plugIn, $config) : plug($plugIn);
            }
        }
    }

    return $init;
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
        return !trigger_error(
            'Plug name should be string. '.print_r($name, true)
        );
    }
    $hasPlug = exists($name, 'plugin');

    // check alias
    $folders = folders(_PLUGIN);
    if (!$hasPlug) {
        $hasPlug = InternalUtility::plugAlias($folders['alias'], $name);
    }

    if ($hasPlug) {
        if (!empty($config)) {
            InternalUtility::plugWithConfig($name, $config);
        }

        return InternalUtility::callPlugInFunc(
            $name,
            'update'
        );
    } else {
        return InternalUtility::generatePlugIn($name, $config, $folders['folders']);
    }
}

/*
 * Plugins -->.
 */
