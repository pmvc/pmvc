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
    implements \ArrayAccess
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
     * Get
     *
     * @param mixed $k key
     *
     * @return mixed
     */
    public function &__get($k=null)
    {
        return $this->offsetGet($k);
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
     * Set
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return boolean
     */
    public function __set($k, $v=null)
    {
        return $this->offsetSet($k, $v);
    }
}
