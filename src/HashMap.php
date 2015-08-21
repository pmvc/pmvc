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
 * PMVC HashMap
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
class HashMap extends ListIterator
    implements \ArrayAccess, \Countable
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
     * @param mixed $k key
     *
     * @return mixed
     */
    public function &offsetGet($k=null)
    {
        return get($this->values, $k);
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
        return set($this->values, $k, $v);
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
        return clean($this->values, $k);
    }

    /**
     * Count
     *
     * @return int
     */
    public function count()
    {
        return count($this->values);
    }
}
