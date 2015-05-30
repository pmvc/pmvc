<?php
/**
 * PMVC
 *
 * PHP version 5
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  GIT: <git_id>
 * @link     http://pear.php.net/package/PackageName
 */
namespace PMVC;

/**
 * Get PMVC control
 *
 * @return ActionController
 */
function getC()
{
    return option('get', CONTROLLER);
}

/**
 * Wrapper get real url
 *
 * @param string $job job
 * @param string $url url
 *
 * @return mixed
 */
function u($job, $url=null)
{
    return call_plugin(
        'url', 'actionToUrl', array($job,$url)
    );
}

/**
 * Transparent
 *
 * @param string $name filename 
 * @param string $app  app name
 *
 * @return string
 */
function transparent($name, $app=null)
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
