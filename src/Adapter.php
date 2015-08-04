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
 * PMVC PlugIn Adapter
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://packagist.org/packages/pmvc/pmvc 
 */
class Adapter implements \ArrayAccess
{
    private $_name;

    /**
     * Assign plugin name, call by cache run
     *
     * @param string $name plugin name 
     *
     * @return mixed
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }

    /**
     * Magic call for function alias
     *
     * @param string $method method
     * @param array  $args   args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $objs = &getOption(PLUGIN_INSTANCE);
        if (!empty($objs[$this->_name])) {
            return call_user_func_array(
                array(
                    $objs[$this->_name],
                    $method
                ),
                $args
            );
        }
    }

    /**
     * Get
     *
     * @param mixed $k key
     *
     * @return mixed 
     */
    public function &offsetGet($k=null)
    {
        $objs = &getOption(PLUGIN_INSTANCE);
        $return = false;
        if (!empty($objs[$this->_name])) {
            $return =& $objs[$this->_name][$k];
        }
        return $return;
    }

    /**
     * Set 
     *
     * @param mixed $k key
     * @param mixed $v value 
     *
     * @return boolean
     */
    public function offsetSet($k, $v=null)
    {
        return $this->__call('offsetSet', array($k,$v));
    }

    /**
     * Clean
     *
     * @param mixed $k key
     *
     * @return boolean
     */
    public function offsetUnset($k=null)
    {
        return $this->__call('offsetUnset', array($k));
    }

    /**
     * ContainsKey
     *
     * @param string $k key 
     *
     * @return boolean
     */
    public function offsetExists($k)
    {
        return $this->__call('offsetExists', array($k));
    }

}
