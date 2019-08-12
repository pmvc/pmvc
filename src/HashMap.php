<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category Data
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

use ArrayAccess;

/**
 * PMVC HashMap
 * What is overloading?
 * http://php.net/manual/en/language.oop5.overloading.php.
 *
 * @category Data
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class HashMap extends ListIterator implements ArrayAccess
{
    /**
     * Get key set.
     *
     * @return array
     */
    public function keySet()
    {
        return array_keys($this->state);
    }

    /**
     * Contains key.
     *
     * @param string $k key
     *
     * @return bool
     */
    public function offsetExists($k)
    {
        return isset($this->state[$k]);
    }

    /**
     * Contains key.
     *
     * @param string $k key
     *
     * @return bool
     */
    public function __isset($k)
    {
        return $this->offsetExists($k);
    }

    /**
     * Get.
     *
     * @param mixed $k key
     *
     * @return mixed
     */
    public function &offsetGet($k = null)
    {
        return get($this->state, $k);
    }

    /**
     * Get reference object.
     *
     * @param mixed $k key
     *
     * @return mixed
     */
    public function __get($k)
    {
        return new BaseObject($this->state[$k]);
    }

    /**
     * Set.
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return bool
     */
    public function offsetSet($k, $v)
    {
        if ([] === $k && is_array($v)) {
            $this->state = array_merge_recursive(
                $this->state,
                $v
            );
        } else {
            set($this->state, $k, $v);
        }
        if ($this->walk) {
            foreach ($this->state as $sk=>$sv) {
                if (is_array($sv)) {
                    $this->state[$sk] = new HashMap($sv, true);
                }
            }
        }

        return $this;
    }

    /**
     * Set.
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return bool
     */
    public function __set($k, $v)
    {
        return $this->offsetSet($k, $v);
    }

    /**
     * Clean.
     *
     * @param mixed $k key
     *
     * @return bool
     */
    public function offsetUnset($k = null)
    {
        clean($this->state, $k);

        return $this;
    }

    /**
     * Clean.
     *
     * @param mixed $k key
     *
     * @return bool
     */
    public function __unset($k = null)
    {
        return $this->offsetUnset($k);
    }
}
