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
 * HashMap.
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
        return array_keys(is_array($this->state) ? $this->state : []);
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
            /**
             * Overwrite with all new value.
             *
             * If the input arrays have the same string keys,
             * then the values for these keys are merged together into an array.
             *
             * https://www.php.net/manual/en/function.array-merge-recursive.php
             */
            $this->state = array_merge_recursive($this->state, $v);
        } elseif ([] === $v && is_array($k) && !empty($k)) {
            // overwrite with not exists key only
            $arrKeys = array_keys($k);
            foreach ($arrKeys as $kk) {
                if (!isset($this->state[$kk])) {
                    $this->state[$kk] = $k[$kk];
                }
            }
        } else {
            set($this->state, $k, $v);
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
