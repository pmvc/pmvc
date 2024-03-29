<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category PlugIn
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
 * @category PlugIn
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
        if (!is_null($this[_PLUGIN_FILE])) {
            return dirname($this[_PLUGIN_FILE]).'/';
        }
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
    #[\ReturnTypeWillChange]
    public function update(SplSubject $subject = null)
    {
        if ($subject) {
            $state = 'on'.$subject->getName();
            $func = $this->isCallable($state);
            if ($func) {
                return call_user_func($func, $subject);
            }
        }

        return $this[THIS];
    }
}
