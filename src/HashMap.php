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
class HashMap extends ListIterator
{
    /**
     * containsKey
     *
     * @param string $key key 
     *
     * @return boolean
     */
    public function containsKey($key)
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * containsValue
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
     * get array_keys 
     *
     * @return boolean
     */
    public function keySet()
    {
        return array_keys($this->values);
    }

    /**
     * get
     *
     * @param mixed $k       key
     * @param mixed $default default value 
     *
     * @return mixed 
     */
    public function get($k=null, $default=null)
    {
        return get($this->values, $k, $default);
    }

    /**
     * set 
     *
     * @param mixed $k key
     * @param mixed $v value 
     *
     * @return bool 
     */
    public function set($k, $v=null)
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
    public function clean($k=null)
    {
        return clean($this->values, $k);
    }
}
