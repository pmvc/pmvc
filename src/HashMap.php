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
use OutOfBoundsException;

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
    #[\ReturnTypeWillChange]
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
    #[\ReturnTypeWillChange]
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
        if (!$this->offsetExists($k)) {
            throw new OutOfBoundsException($k.' is not in hashmap.');
        }

        return new BaseObject($this->offsetGet($k));
    }

    /**
     * Last effect keys store.
     *
     * @param array $keys last effect keys.
     *
     * @return mixed
     */
    protected function lastKeys($keys = null)
    {
        static $_lastKeys;
        if (!is_null($keys)) {
            $_lastKeys = $keys;
        }

        return $_lastKeys;
    }

    /**
     * Set.
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($k, $v)
    {
        if ([] === $k) {
            if (isArrayAccess($v)) {
                $v = get($v);
            }
            if (is_array($v)) {
                /**
                 * Overwrite with all new value.
                 *
                 * If the input arrays have the same string keys,
                 * then the values for these keys are merged together into an array.
                 *
                 * https://www.php.net/manual/en/function.array-merge-recursive.php
                 */
                $this->lastKeys(array_keys($v));
                $this->state = array_merge_recursive($this->state, $v);
            } else {
                if (!is_callable($v)) {
                    return triggerJson(
                        'Hashmap merge mode only accept array or function call.',
                        compact('v')
                    );
                }
                $next = $v();
                $this->lastKeys(array_keys($next));
                $this->state = array_replace_recursive($this->state, $next);
            }
        } elseif ([] === $v && is_array($k) && !empty($k)) {
            // overwrite with not exists key only
            $arrKeys = $this->lastKeys(array_keys($k));
            foreach ($arrKeys as $kk) {
                if (!isset($this->state[$kk])) {
                    $this->state[$kk] = $k[$kk];
                }
            }
        } else {
            $this->lastKeys(set($this->state, $k, $v));
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
    #[\ReturnTypeWillChange]
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
