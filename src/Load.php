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

namespace PMVC;

/**
 * PMVC Action.
 *
 * @category CategoryName
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class Load
{
    /**
     * Include plugin only.
     *
     * @param array $init    Default plugins
     * @param array $folders Extra plugin folder
     * @param array $options PMVC options
     *
     * @return bool
     */
    public static function plug(
        array $init = [],
        array $folders = [],
        array $options = []
    ) {
        include_once __DIR__.'/../include.php';
        if (!empty($options)) {
            \PMVC\option('set', $options);
        }
        self::initPlugInFolder();
        if (!empty($folders)) {
            addPlugInFolders($folders);
        }
        if (!empty($init)) {
            initPlugin($init, \PMVC\get($options, PAUSE));
        }
    }

    /**
     * Init plug folder.
     *
     * @return bool
     */
    public static function initPlugInFolder()
    {
        addPlugInFolders([__DIR__.'/../../../pmvc-plugin']);
    }
}
