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
 * PMVC Action
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     http://pear.php.net/package/PackageName
 */
class Load
{
    /**
     * include mvc module, contain plugin 
     *
     * @return boolean
     */
    public static function mvc()
    {
        include __DIR__.'/../include.php';
        self::initPlugInFolder();
    }

    /**
     * include plugin only 
     *
     * @return boolean
     */
    public static function plug()
    {
        include __DIR__.'/../include_plug.php';
        self::initPlugInFolder();
    }

    /**
     * init plug folder 
     *
     * @return boolean
     */
    public static function initPlugInFolder()
    {
        $dir = __DIR__.'/../../../pmvc-plugin';
        if (is_dir($dir)) {
            setPlugInFolder($dir);
        }
    }
}
