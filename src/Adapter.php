<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category PlugIn
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
use SplObserver;
use SplSubject;

/**
 * PMVC PlugIn Adapter.
 *
 * Adapter help you not leak plug-in class instance to global,
 * and especially useful in unplug.
 * It purpose let plugin's attribute can not access even it's public,
 * If you need access variable, need replace $plug->xxx with $plug['xxx'].
 *
 * @category PlugIn
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class Adapter implements ArrayAccess, SplObserver
{
    private $_name;

    /**
     * Assign plugin name, call by cache run.
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
     * Magic call for function alias.
     *
     * @param string $method method
     * @param array  $args   args
     *
     * @return mixed
     */
    public function __call($method, $args = [])
    {
        return InternalUtility::callPlugInFunc(
            $this->_name,
            $method,
            $args
        );
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->__call(__FUNCTION__);
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
        $params = [$k];
        if (is_string($k) && $this->__call('offsetExists', $params)) {
            $val = $this->__call('__get', $params);
            $val = &ref($val);
        } else {
            $val = $this->__call(__FUNCTION__, $params);
        }

        return $val;
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
    public function offsetSet($k, $v = null)
    {
        return $this->__call(__FUNCTION__, [$k, $v]);
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
        return $this->__call(__FUNCTION__, [$k]);
    }

    /**
     * ContainsKey.
     *
     * @param string $k key
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($k)
    {
        return $this->__call(__FUNCTION__, [$k]);
    }

    /**
     * Observer update function.
     *
     * @param SplSubject $subject observable
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function update(SplSubject $subject = null)
    {
        return $this->__call(__FUNCTION__, [$subject]);
    }
}
