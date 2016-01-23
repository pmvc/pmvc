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
use SplObjectStorage;

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
    protected $keys = array();

    /**
     * ContainsKey
     *
     * @param string $k key
     *
     * @return boolean
     */
    public function offsetExists($k)
    {
        return isset($this->keys[$k]);
    }

    /**
     * Get Initial State 
     *
     * @return array 
     */
    protected function getInitialState()
    {
        return new SplObjectStorage();
    }

    /**
     * Get array_keys
     *
     * @return boolean
     */
    public function keySet()
    {
        return $this->keys;
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
        if (!isset($this->keys[$k])) {
            $this->keys[$k] = new \StdClass;
        }
        return $this->state[$this->keys[$k]]
            = new Object($v);
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
        $key = $this->keys[$k];
        unset($this->state[$key]);
        unset($this->keys[$k]);
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
        if (is_null($k)) {
            $val = array();
            foreach ($this->keys as $k=>$v) {
                $val[$k] = $this->state[$v]();
            }
        } else {
            if (isset($this->keys[$k])) {
                $val = $this->state[$this->keys[$k]]();
            } else {
                $val = null;
            }
        }
        return $val;
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
        if (isset($this->keys[$k])) {
            $val =  $this->state[$this->keys[$k]]; 
        } else {
            $val = null;
        }
        return $val;
    }
}
