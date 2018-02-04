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
    if (defined('_CLASS')) {
        return;
    }
    /* Plugin */
    define('_CLASS', '_class_'); //action, plugin
    define('_PLUGIN', '_plugin_');
    define('_PLUGIN_FILE', '_plugin_file_');
    define('_INIT_CONFIG', '_init_config_');
    define('_LAZY_CONFIG', '_lazy_config_');
    define('_IS_SECURITY', '_is_security_');

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
    const PAUSE = '__pause__';
    const NAME = '__name__';
    const THIS = 'this';
}

namespace PMVC\Event {
    const B4_PROCESS_ACTION = 'B4ProcessAction';
    const B4_PROCESS_ERROR = 'B4ProcessError';
    const B4_PROCESS_MAPPING = 'B4ProcessMapping';
    const B4_PROCESS_HEADER = 'B4ProcessHeader';
    const B4_PROCESS_VIEW = 'B4ProcessView';
    const FINISH = 'Finish';
    const MAP_REQUEST = 'MapRequest';
    const SET_CONFIG = 'SetConfig';
}
