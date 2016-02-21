<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category CategoryName
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
 * PMVC Action.
 *
 * @category CategoryName
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class Load
{
    /**
     * Include mvc module, contain plugin.
     *
     * @return bool
     */
    public static function mvc()
    {
        include __DIR__.'/../include.php';
        self::initPlugInFolder();
    }

    /**
     * Include plugin only.
     *
     * @return bool
     */
    public static function plug()
    {
        include __DIR__.'/../include_plug.php';
        self::initPlugInFolder();
    }

    /**
     * Init plug folder.
     *
     * @return bool
     */
    public static function initPlugInFolder()
    {
        $dir = __DIR__.'/../../../pmvc-plugin';
        if (is_dir($dir)) {
            setPlugInFolder($dir);
        }
    }
}
