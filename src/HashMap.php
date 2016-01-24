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
 * What is overloading?
 * http://php.net/manual/en/language.oop5.overloading.php
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
     * Get array_keys
     *
     * @return boolean
     */
    public function keySet()
    {
        return array_keys($this->state);
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
        return isset($this->state[$k]);
    }

    /**
     * Get 
     *
     * @param mixed $k key
     *
     * @return boolean
     */
    public function &offsetGet($k=null)
    {
        return get($this->state, $k);
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
        $val = new Object($this->state[$k]);
        return $val;
    }

    /**
     * Set
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return boolean
     */
    public function offsetSet($k, $v)
    {
        return $this->state[$k] = $v;
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

    /**
     * Clean
     *
     * @param mixed $k key
     *
     * @return boolean
     */
    public function offsetUnset($k=null)
    {
        return clean($this->state, $k);
    }

}
