<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category Data
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
 * PMVC root object.
 *
 * @category Data
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class BaseObject
{
    protected $state;

    /**
     * Construct.
     *
     * @param mixed $state state
     */
    public function __construct(&$state = null)
    {
        $this->state = &$state;
    }

    /**
     * Called when a script tries to call an object as a function.
     * available since PHP 5.3.0.
     *
     * @param mixed $state state
     *
     * @return mixed
     */
    public function &__invoke($state = null)
    {
        if (!is_null($state)) {
            $this->state = $state;
        }

        return $this->state;
    }
}
