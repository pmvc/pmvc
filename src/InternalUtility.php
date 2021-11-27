<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category PlugIn
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

use DomainException;
use stdClass;

/**
 * Utilitys for internal use only.
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
class InternalUtility
{
    /**
     * Private plugin store.
     *
     * @var array Plugin store.
     */
    private static $_plugins = [];

    /**
     * Private function for l.
     *
     * @param string $name   File name.
     * @param mixed  $export Extract one variable name.
     * @param array  $import Import variable to file.
     *
     * @return mixed
     */
    public static function l($name, $export = null, $import = null)
    {
        if (is_array($import)) {
            foreach ($import as $k => $v) {
                $$k = $v;
            }
        }
        include $name;
        $o = new stdClass();
        $o->name = $name;
        if (isset($$export)) {
            $o->var = compact($export);
        }

        return $o;
    }

    /**
     * Realpath auto append .php.
     *
     * @param string $name path name
     *
     * @return string
     */
    public static function realPathPhpNoCache($name)
    {
        $ext = substr($name, -4, 4);
        $append = '.php';
        $real = null;
        if ($ext !== $append) {
            $real = realpath($name.$append);
        }
        if (!$real) {
            $real = realpath($name);
        }

        return $real;
    }

    /**
     * Cache realpath auto append .php.
     *
     * @param string $name path name
     *
     * @return string
     */
    public static function realPathPhp($name)
    {
        return run(
            ns('InternalUtility::realPathPhpNoCache'),
            [$name]
        );
    }

    /**
     * Check plugin is already plug.
     *
     * @param string $name File name.
     *
     * @return bool
     */
    public static function isPlugInExists($name)
    {
        $cookName = strtolower($name);

        return !empty(self::$_plugins[$cookName]);
    }

    /**
     * Call plugin function.
     *
     * @param string $name   plugin.
     * @param string $method method.
     * @param array  $args   args.
     *
     * @return mixed
     */
    public static function callPlugInFunc($name, $method, $args = [])
    {
        $oPlugIn = get(self::$_plugins, strtolower($name));
        if (!empty($oPlugIn)) {
            return call_user_func_array(
                [
                    $oPlugIn,
                    $method,
                ],
                $args
            );
        }
    }

    /**
     * Get Plugins.
     *
     * @return array
     */
    public static function getPlugInNameList()
    {
        return array_keys(self::$_plugins);
    }

    /**
     * PlugIn Store for Security.
     *
     * @param string $name       plug-in name
     * @param PlugIn $value      [false: unset] [other: set]
     * @param bool   $isSecurity security flag
     *
     * @return mixed
     */
    public static function plugInStore(
        $name,
        $value,
        $isSecurity = false
    ) {
        static $securitys = [];
        $plugins = &self::$_plugins;
        $hadPlug = false;
        $hasSecurity = false;
        $cookName = strtolower($name);
        if (isset($plugins[$cookName])) {
            $hadPlug = true;
        }
        if (isset($securitys[$cookName])) {
            $hasSecurity = $securitys[$cookName];
        }

        if ($hadPlug && false !== $value && ($isSecurity || $hasSecurity)) {
            throw new DomainException(
                'Security plugin ['.
                    $name.
                    '] already plug or unplug, '.
                    'you need check your code if it is safe.'
            );
        }

        if ($hasSecurity) {
            return !trigger_error(
                'You can not change security plugin. ['.$name.']'
            );
        } else {
            if (empty($value)) { // false === $value
                unset($plugins[$cookName]);
                $plugins[$cookName] = false;
            } else {
                $plugins[$cookName] = $value;
            }
            if ($isSecurity) {
                $securitys[$cookName] = true;
            }

            return (bool) $hadPlug;
        }
    }

    /**
     * Plug alias.
     *
     * @param array  $aliasList Alias list.
     * @param string $aliasName New alias name.
     *
     * @return PlugIn
     */
    public static function plugAlias($aliasList, $aliasName)
    {
        $cookName = strtolower($aliasName);
        $targetPlugin = get($aliasList, $cookName);
        if (!empty($targetPlugin)) {
            $oPlugin = get(self::$_plugins, $targetPlugin);
            if (empty($oPlugin)) {
                throw new DomainException(
                    'Plug alias fail. Target: ['.
                        $targetPlugin.
                        '], New Alias: ['.
                        $aliasName.
                        ']'
                );
            }
            InternalUtility::plugInStore($aliasName, $oPlugin);

            return $targetPlugin;
        }
    }

    /**
     * Plug.
     *
     * @param string $name    Plug-in name.
     * @param array  $config  Plug-in configs.
     * @param array  $folders Plug-in folder.
     * @param bool   $pause   Pause.
     *
     * @return PlugIn
     */
    public static function initPlugInObject(
        $name,
        &$config,
        $folders = null,
        $pause = false
    ) {
        // get config from global options
        $names = explode('_', $name);
        $config = array_replace(
            value(getOption('PLUGIN'), $names, []),
            value(getOption('PW'), $names, []),
            $config
        );
        $file = null;

        if (isset($config[_CLASS]) && class_exists($config[_CLASS])) {
            $class = $config[_CLASS];
        } else {
            if (isset($config[_PLUGIN_FILE])) {
                $file = $config[_PLUGIN_FILE];
                $r = l($file, _INIT_CONFIG, ['ignore' => true]);
                if (empty($r)) {
                    return !trigger_error(
                        'PlugIn '.
                            $name.
                            ': defined file not found. '.
                            '['.
                            $file.
                            ']'
                    );
                } else {
                    $config[_PLUGIN_FILE] = $r->name;
                }
            } else {
                $file = $name.'/'.$name;
                $r = load(
                    $file,
                    $folders,
                    _INIT_CONFIG,
                    true,
                    false
                );
            }
            $class = !empty($r) && !is_int($r) ? get(
                $r->var[_INIT_CONFIG],
                _CLASS,
                function () use (
                    $config
                ) {
                    return get($config, _DEFAULT_CLASS);
                }
            ) : null;
        }
        $exists = class_exists($class);
        if ($pause) {
            return $exists; //for inclue only purpose
        }
        if ($exists) {
            $oPlugIn = new $class();
            if (!($oPlugIn instanceof PlugIn)) {
                return !trigger_error(
                    'Class is not a plug-in('.ns('PlugIn').') instance.'
                );
            }
        } else {
            if (!$class) {
                $error = 'Plug-in '.$name.' not found.';
                if (!empty($file)) {
                    $error .=
                        ' ['.
                        $file.
                        '] '.
                        print_r($folders, true);
                }
            } else {
                $error = 'Plug-in '.$name.': class not found ('.$class.')';
            }

            return !trigger_error($error);
        }
        if (!empty($r)) {
            if (isset($r->var) && isArray($r->var[_INIT_CONFIG])) {
                $config = arrayReplace($r->var[_INIT_CONFIG], $config);
            }
            $config[_PLUGIN_FILE] = $r->name;
        }

        return $oPlugIn;
    }

    /**
     * Generate Plug-In.
     *
     * @param string $name    Plug-in name.
     * @param array  $config  Plug-in configs.
     * @param array  $folders Plug-in folder.
     *
     * @return PlugIn
     */
    public static function generatePlugIn(
        $name,
        array $config = [],
        $folders = null
    ) {
        $oPlugIn = self::initPlugInObject($name, $config, $folders);
        rePlug($name, $config, $oPlugIn);
        $oPlugIn->init();
        if (false === strpos('|debug|dev|cli|', $name)) {
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
                },
                'plug'
            );
        }

        return $oPlugIn->update();
    }

    /**
     * Plug With Config.
     *
     * @param string $name   Plug-in name
     * @param array  $config Plug-in configs
     *
     * @return void
     */
    public static function plugWithConfig($name, array $config)
    {
        $oPlugIn = get(self::$_plugins, strtolower($name));
        InternalUtility::setPlugInConfig($oPlugIn, $config);
    }

    /**
     * Set plugin config.
     *
     * @param PlugIn $oPlugIn Plug-in object
     * @param array  $config  Plug-in configs
     *
     * @return void
     */
    public static function setPlugInConfig($oPlugIn, array $config)
    {
        if (!empty($oPlugIn) && !empty($config)) {
            if (is_callable(get($config, _LAZY_CONFIG))) {
                $config = array_replace($config, $config[_LAZY_CONFIG]());
            }
            set($oPlugIn, $config);
        }
    }

    /**
     * Set plugin config.
     *
     * @param mixed $name get name from object
     *
     * @return string
     */
    public static function getPlugName($name)
    {
        return is_string($name) ? $name : get($name, NAME);
    }
}
