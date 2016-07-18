<?php
/**
 * PMVC.
 *
 * This file only use in
 * "Global Option", "Mapping Option", "Plugin Option".
 * Other constant should put in namespace.constants.php
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
namespace {
    if (defined('_CLASS')) {
        return;
    }
    /* Plugin */
    define('_CLASS', '_class_'); //action, plugin
    define('_PLUGIN', '_plugin_');
    define('_PLUGIN_FILE', '_plugin_file_');
    define('_INIT_CONFIG', '_init_config_');

    /* Debug */
    define('_VIEW_ENGINE', '_view_engine_');
}

namespace PMVC {
    /**
     * System Error.
     */
    const ERRORS = '__errors__';
    const SYSTEM_ERRORS = '__system_errors__';
    const SYSTEM_LAST_ERROR = '__system_last_error__';
    //user_error
    const USER_ERRORS = '__user_errors__';
    const USER_LAST_ERROR = '__user_last_error__';
    //user_warn, user_notice
    const APP_ERRORS = '__app_errors__';
    const APP_LAST_ERROR = '__app_last_error__';

    /**
     * Plugins.
     */
    const PLUGIN_INSTANCE = '__plugin_instance__';
    const PAUSE = '__pause__';
}
