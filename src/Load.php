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
 * PMVC Loader.
 *
 * @category Core
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
     * @param mixed $init    Default plugins or lazy funciton
     * @param array $folders Extra plugin folder
     * @param array $options PMVC options
     *
     * @return bool
     */
    public static function plug(
        $init = [],
        array $folders = [],
        array $options = []
    ) {
        self::initPlugInFolder();
        if (is_callable($init)) {
            $params = $init();
            $init = $params[0];
            $folders = $params[1];
            $options = $params[2];
        }
        if (!empty($options)) {
            \PMVC\option('set', $options);
        }
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
