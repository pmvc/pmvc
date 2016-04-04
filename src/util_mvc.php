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

/**
 * Get PMVC control.
 *
 * @return ActionController
 */
function getC()
{
    static $c;
    if (empty($c)) {
        $c = new ActionController();
    }

    return $c;
}

/**
 * Transparent.
 *
 * @param string $name filename
 * @param string $app  app name
 *
 * @return string
 */
function transparent($name, $app = null)
{
    if (is_null($app)) {
        $app = getC()->getApp();
    }
    $folder = getC()->getAppParent();
    if (!$folder) {
        return $name;
    }
    $appFile = lastSlash($folder).$app.'/'.$name;
    $appFile = realpath($appFile);
    if ($appFile) {
        return $appFile;
    } else {
        return $name;
    }
}

/**
 * Set App Folder.
 *
 * @param array $folders folders
 * @param array $alias   alias
 *
 * @return mixed
 */
function setAppFolder($folders, $alias = [])
{
    return folders(_RUN_APP, $folders, $alias, true);
}

/**
 * Add App Folder.
 *
 * @param array $folders folders
 * @param array $alias   alias
 *
 * @return mixed
 */
function addAppFolder($folders, $alias = [])
{
    return folders(_RUN_APP, $folders, $alias);
}
