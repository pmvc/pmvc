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
if (defined('_CLASS')) {
    return;
}

/* Plugin */
define('_CLASS', '_class_'); //action, plugin
define('_PLUGIN', '_plugin_');
define('_PLUGIN_FILE', '_plugin_file_');
define('_INIT_CONFIG', '_init_config_');

/* Error */
define('_ERROR_REPORTING', '_error_reporting_');
define('_ERROR_ENABLE_LOG', '_error_enable_log_');

/* Debug */
define('_VIEW_ENGINE', '_view_engine_');

/* Env */
define('_ENV_FILE', '_env_file_');
