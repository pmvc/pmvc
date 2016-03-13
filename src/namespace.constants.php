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

/*
 * Action
 */
if (defined('\PMVC\ACTION_FORMS')) {
    return;
}
const ACTION_FORMS = '__action_forms__';
const ACTION_MAPPINGS = '__action_mappings__';
const ACTION_FORWARDS = '__action_forwards__';

/**
 * Plugin.
 */
const PLUGIN_INSTANCE = '__plugin_instance__';
const PLUGIN_FOLDERS = '__plugin_folders__';
const PLUGIN_ALIAS = '__plugin_alias__';

/**
 * System Error.
 */
const PAUSE = '__pause__';
const ERRORS = '__errors__';
const SYSTEM_ERRORS = '__system_errors__';
const SYSTEM_LAST_ERROR = '__system_last_error__';
//user_error
const USER_ERRORS = '__user_errors__';
const USER_LAST_ERROR = '__user_last_error__';
//user_warn, user_notice
const APP_ERRORS = '__app_errors__';
const APP_LAST_ERROR = '__app_last_error__';
