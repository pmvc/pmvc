<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category Plug
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
 * PMVC PlugIn Adapter
 * It purpose let plugin's attribute can not access even it's public,
 * if you need use it, need replace with $plug['xxx'].
 *
 * @category CategoryName
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
        $objs = getOption(PLUGIN_INSTANCE);
        if (!empty($objs[$this->_name])) {
            return call_user_func_array(
                [
                    $objs[$this->_name],
                    $method,
                ],
                $args
            );
        }
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __tostring()
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
    public function &offsetGet($k = null)
    {
        $val = $this->__call(__FUNCTION__, [$k]);

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
    public function update(SplSubject $subject = null)
    {
        return $this->__call(__FUNCTION__, [$subject]);
    }
}
