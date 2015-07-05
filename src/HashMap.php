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
 * @link     http://pear.php.net/package/PackageName
 */
namespace PMVC;

/**
 * PMVC HashMap
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     http://pear.php.net/package/PackageName
 */
class HashMap extends ListIterator implements \ArrayAccess
{
    /**
     * ContainsValue
     *
     * @param string $value value 
     *
     * @return boolean
     */
    public function containsValue($value)
    {
        return in_array($value, $this->values);
    }

    /**
     * Get array_keys 
     *
     * @return boolean
     */
    public function keySet()
    {
        return array_keys($this->values);
    }

    /**
     * ContainsKey
     *
     * @param string $key key 
     *
     * @return boolean
     */
    public function offsetExists($key)
    {
        return isset($this->values[$key]);
    }

    /**
     * Get
     *
     * @param mixed $k       key
     * @param mixed $default default value 
     *
     * @return mixed 
     */
    public function offsetGet($k=null, $default=null)
    {
        return get($this->values, $k, $default);
    }

    /**
     * Set 
     *
     * @param mixed $k key
     * @param mixed $v value 
     *
     * @return bool 
     */
    public function offsetSet($k, $v=null)
    {
        return set($this->values, $k, $v);
    }

    /**
     * Clean
     *
     * @param mixed $k key
     *
     * @return bool 
     */
    public function offsetUnset($k=null)
    {
        return clean($this->values, $k);
    }
}
