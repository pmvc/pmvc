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
     * PlugIn Store for Security.
     *
     * @param string $key        plug-in name
     * @param PlugIn $value      [null: get only] [false: unset] [other: set]
     * @param bool   $isSecurity security flag
     *
     * @return mixed
     */
    public static function plugInStore(
        $key = null,
        $value = null,
        $isSecurity = false
    ) {
        static $plugins = [];
        static $securitys = [];
        $currentPlug = false;
        $hasSecurity = false;
        if (!is_null($key)) {
            $cookKey = strtolower($key);
            if (isset($plugins[$cookKey])) {
                $currentPlug = $plugins[$cookKey];
            }
            if (isset($securitys[$cookKey])) {
                $hasSecurity = $securitys[$cookKey];
            }
        }
        if (is_null($value)) {
            if ($currentPlug) {
                return $currentPlug;
            } elseif (is_null($key)) {
                return array_keys($plugins);
            } else {
                return false;
            }
        }
        if ($currentPlug && false !== $value && ($isSecurity || $hasSecurity)) {
            throw new DomainException(
                'Security plugin ['.
                    $key.
                    '] already plug or unplug, '.
                    'you need check your code if it is safe.'
            );
        }
        if ($hasSecurity) {
            return !trigger_error(
                'You can not change security plugin. ['.$key.']'
            );
        } else {
            $plugins[$cookKey] = $value;
            if ($isSecurity) {
                $securitys[$cookKey] = true;
            }

            return $currentPlug;
        }
    }

    /**
     * Plug alias.
     *
     * @param string $targetPlugin Target plugin.
     * @param string $aliasName    New alias name.
     *
     * @return PlugIn
     */
    public static function plugAlias($targetPlugin, $aliasName)
    {
        $oPlugin = InternalUtility::plugInStore($targetPlugin);
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

        return $oPlugin;
    }

    /**
     * Plug.
     *
     * @param array  $folders Plug-in folder.
     * @param array  $plugTo  New name in plugin folder.
     * @param string $name    Plug-in name
     * @param array  $config  Plug-in configs
     *
     * @return PlugIn
     */
    public static function plugInGenerate(
        $folders,
        $plugTo,
        $name,
        array $config = []
    ) {
        // get config from global options
        $names = explode('_', $name);
        $config = array_replace(
            value(getOption('PLUGIN'), $names, []),
            value(getOption('PW'), $names, []),
            $config
        );

        if (isset($config[_CLASS]) && class_exists($config[_CLASS])) {
            $class = $config[_CLASS];
        } else {
            $file = null;
            if (isset($config[_PLUGIN_FILE])) {
                $file = realpath($config[_PLUGIN_FILE]);
                if (empty($file)) {
                    return !trigger_error(
                        'PlugIn '.
                            $name.
                            ': defined file not found. '.
                            '['.
                            $config[_PLUGIN_FILE].
                            ']'
                    );
                }
                // assign realpath
                $config[_PLUGIN_FILE] = $file;
            }
            if ($file) {
                $r = l($file, _INIT_CONFIG);
            } else {
                $file = $name.'/'.$name.'.php';
                $r = load(
                    $file,
                    $folders['folders'],
                    _INIT_CONFIG,
                    true,
                    false
                );
            }
            $class = value(
                $r,
                ['var', _INIT_CONFIG, _CLASS],
                function () use (
                    $config
                ) {
                    return get($config, _DEFAULT_CLASS);
                }
            );
        }
        $exists = class_exists($class);
        if (!empty($config[PAUSE])) {
            return $exists; //for inclue only purpose
        }
        if ($exists) {
            $oPlugin = new $class();
            if (!($oPlugin instanceof PlugIn)) {
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
                        print_r($folders['folders'], true);
                }
            } else {
                $error = 'Plug-in '.$name.': class not found ('.$class.')';
            }

            return !trigger_error($error);
        }
        if (!empty($r)) {
            if (isset($r->var[_INIT_CONFIG])) {
                $config = arrayReplace($r->var[_INIT_CONFIG], $config);
            }
            $config[_PLUGIN_FILE] = $r->name;
        }
        InternalUtility::plugWithConfig($oPlugin, $config);
        rePlug($plugTo, $oPlugin);
        $oPlugin->init();
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

        return $oPlugin->update();
    }

    /**
     * Plug With Config.
     *
     * @param PlugIn $oPlugin Plug-in object
     * @param array  $config  Plug-in configs
     *
     * @return void
     */
    public static function plugWithConfig($oPlugin, array $config)
    {
        if (!empty($oPlugin) && !empty($config)) {
            if (is_callable(get($config, _LAZY_CONFIG))) {
                $config = array_replace($config, $config[_LAZY_CONFIG]());
            }
            set($oPlugin, $config);
        }
    }
}
