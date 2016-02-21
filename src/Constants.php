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

/* mappings constant(s) */
if (defined('_CLASS')) {
    return;
}
define('_CLASS', '_class_'); //action, plugin
define('_FORM', '_form_'); //action
define('_VALIDATE', '_validate_'); //action
define('_SCOPE', '_scope_'); //action
define('_TYPE', '_type_');
define('_FORWARD', '_forward_');
define('_PATH', '_path_');
define('_FUNCTION', '_function_');
define('_OPTION', '_option_');

/* mapping-forward */
define('_ACTION', '_action_');
define('_HEADER', '_header_');

/* options constant(s) */
define('_ROUTING', '_routing_');
define('_VIEW_ENGINE', '_view_engine_');
define('_TEMPLATE_DIR', '_template_dir_');
// Error
define('_ERROR_REPORTING', '_error_reporting_');
define('_ERROR_ENABLE_LOG', '_error_enable_log_');

/*
 * Plugin
 */
define('_PLUGIN', '_plugin_');
define('_PLUGIN_FILE', '_plugin_file_');
define('_INIT_CONFIG', '_init_config_');
define('_INIT_BUILDER', '_init_builder_');

/*
 * Run
 */
define('_DEFAULT_APP', '_default_app_');
define('_DEFAULT_FORM', '_default_form_');
define('_RUN_APP', '_run_app_');
define('_RUN_ACTION', '_run_action_');
define('_RUN_FORM', '_run_form_');
define('_RUN_PARENT', '_run_parent_');
