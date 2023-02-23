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

namespace {
    
    if (defined('\PMVC\ERRORS')) {
        return;
    }
    /* Plugin */
    if (!defined('_CLASS')) {
        define('_CLASS', '_class_'); //use by action, plugin
    }
    if (!defined('_DEFAULT_CLASS')) {
        define('_DEFAULT_CLASS', '_default_class_');
    }
    if (!defined('_PLUGIN')) {
        define('_PLUGIN', '_plugin_');
    }
    if (!defined('_PLUGIN_FILE')) {
        define('_PLUGIN_FILE', '_plugin_file_');
    }
    if (!defined('_INIT_CONFIG')) {
        define('_INIT_CONFIG', '_init_config_');
    }
    if (!defined('_LAZY_CONFIG')) {
        define('_LAZY_CONFIG', '_lazy_config_');
    }
    if (!defined('_IS_SECURITY')) {
        define('_IS_SECURITY', '_is_security_');
    }
    if (!defined('_VIEW_ENGINE')) {
        define('_VIEW_ENGINE', '_view_engine_'); //use by debug
    }
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
    const NAME = '__name__';
    const THIS = 'this';
}

namespace PMVC\Event {
    const SET_CONFIG = 'SetConfig';
    const MAP_REQUEST = 'MapRequest';
    const WILL_SET_VIEW = 'WillSetView';
    const WILL_PROCESS_ERROR = 'WillProcessError';
    const WILL_PROCESS_ACTION = 'WillProcessAction';
    const WILL_PROCESS_HEADER = 'WillProcessHeader';
    const WILL_PROCESS_VIEW = 'WillProcessView';
    const FINISH = 'Finish';
}
