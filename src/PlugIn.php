<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category Plug
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

use SplObserver;
use SplSubject;

/**
 * PMVC PlugIn.
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
class PlugIn extends HashMap implements SplObserver
{
    /*
     * Alias
     */
    use Alias;

    /**
     * Get dir.
     *
     * @return mixed
     */
    public function getDir()
    {
        return dirname($this[_PLUGIN_FILE]).'/';
    }

    /**
     * Init.
     *
     * @return mixed
     */
    public function init()
    {
    }

    /**
     * Instanceof.
     *
     * @param object $obj object
     *
     * @return bool
     */
    public function is($obj)
    {
        return $this instanceof $obj;
    }

    /**
     * Observer update function.
     *
     * @param SplSubject $subject observable
     *
     * @return mixed
     */
    public function update(SplSubject $subject = null)
    {
        if ($subject) {
            $state = 'on'.$subject->getName();
            if ($this->isCallable($state)) {
                return $this->{$state}($subject);
            }
        }

        return $this[THIS];
    }
}
