<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category CategoryName
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
 * @category CategoryName
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class Object
{
    protected $state;

    /**
     * Construct.
     *
     * @param array $state state
     */
    public function __construct(&$state = null)
    {
        $this->state = &$state;
    }

    /**
     * Clled when a script tries to call an object as a function.
     * available since PHP 5.3.0.
     *
     * @param array $state state
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
