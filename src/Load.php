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
    static public function mvc($params=array())
    {
        include(__DIR__.'/../include.php');     
        self::initPlugInFolder();
    }

    static public function plug($params=array())
    {
        include(__DIR__.'/../include_plug.php');     
        self::initPlugInFolder();
    }

    static public function initPlugInFolder($params=array())
    {
        $dir = __DIR__.'/../../../pmvc-plugin';
        if(is_dir($dir)){
            setPlugInFolder($dir);
        }
    }
}
