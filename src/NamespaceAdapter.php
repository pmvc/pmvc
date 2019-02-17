<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category Alias
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
 * Magic wrap for namespace function.
 *
 * @category Alias
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class NamespaceAdapter
{
    private $_name;

    /**
     * Construct.
     *
     * @param string $name namespace
     */
    public function __construct($name)
    {
        $this->_name = '\\'.$name.'\\';
    }

    /**
     * Magic call for function alias.
     *
     * @param string $method method
     * @param array  $args   args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $func = $this->isCallable($method);
        if ($func) {
            return call_user_func_array(
                $func,
                $args
            );
        }
    }

    /**
     * Custom is_callable for Alias.
     *
     * @param string $method Method
     *
     * @return function
     */
    public function isCallable($method)
    {
        $func = $this->_name.$method;
        if (function_exists($func)) {
            return $func;
        }
    }
}
