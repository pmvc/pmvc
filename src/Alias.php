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
 * Alias
 */
trait Alias
{
    private $_aliases = array();

    /**
     * Magic call for function alias
     *
     * @param string $method method
     * @param array  $args   args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (isset($this->_aliases[$method])) {
            $func = $this->_aliases[$method];
        } elseif (isset($this->_aliases[''])) {
            $func = array($this->_aliases[''],$method);
            if (!is_callable($func)) {
                $func = false; 
            }
        } 
        if (empty($func)) {
            if (is_callable($this[$method])) {
                $func = $this[$method];
            } else {
                return !trigger_error(
                    'Method not found: '.
                    get_class($this).
                    '::'.
                    $method
                );
            }
        }
        if (!empty($func)) {
            return call_user_func_array(
                $func,
                $args
            );
        }
    }

    /**
     * SetDefaultAlias
     *
     * @param object $obj class instance
     *
     * @return mixed
     */
    public function setDefaultAlias($obj)
    {
        $this->setAlias('', $obj);
    }

    /**
     * CleanDefaultAlias
     *
     * @return mixed
     */
    public function cleanDefaultAlias()
    {
        $this->cleanAlias('');
    }

    /**
     * SetAlias
     *
     * @param string $k method name
     * @param mixed  $v alias to new method (function or class method)
     *
     * @return mixed
     */
    public function setAlias($k, $v=null)
    {
        set($this->_aliases, $k, $v);
    }

    /**
     * CleanAlias
     *
     * @param array $arr array
     *
     * @return mixed
     */
    public function cleanAlias($arr=null)
    {
        clean($this->_aliases, $arr);
    }
}
