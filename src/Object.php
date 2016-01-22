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
 * PMVC root object
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
class Object
{
    protected $values;

    /**
     * Construct
     *
     * @param array $values values
     */
    public function __construct(&$values=array())
    {
        $this->values =& $values;
    }

    /**
     * Clled when a script tries to call an object as a function.
     * available since PHP 5.3.0.
     * 
     * @return mixed 
     */
    public function &__invoke()
    {
        return $this->values;
    }

}
