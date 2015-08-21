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
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
namespace PMVC;

/**
 * PMVC PlugIn
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
class PlugIn extends HashMap implements \SplObserver
{
    /**
     * Alias
     */
     use Alias;

    /**
     * Get dir
     *
     * @return mixed
     */
    public function getDir()
    {
        return dirname($this[_PLUGIN_FILE]).'/';
    }

    /**
     * Init
     *
     * @return mixed
     */
    public function init()
    {
    }

    /**
     * Observer update function
     *
     * @param \SplSubject $subject observable
     *
     * @return mixed
     */
    public function update(\SplSubject $subject=null)
    {
        if ($subject) {
            $state = $subject->getName();
            if (method_exists($this, 'on'.$state)) {
                $r=call_user_func(
                    array($this, 'on'.$state),
                    $subject,
                    $state
                );
                return $r;
            }
        }
        return $this['this'];
    }
}
