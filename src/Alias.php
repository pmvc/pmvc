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
 * Alias
 */
trait Alias 
{
    private $_alias;

    /**
     * magic call for function alias
     *
     * @param string $method method
     * @param array  $args   args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (isset($this->_alias[$method])) {
            $r=call_user_func_array(
                $this->_alias[$method],
                $args
            );
        } else {
            if (method_exists($this->_alias[''], $method)) {
                $r=call_user_func_array(
                    array($this->_alias[''],$method),
                    $args
                );
            } else {
                trigger_error('Method not found: '.get_class($this).'::'.$method);
            }
        }
        if (isset($r)) {
            return $r;
        }
    }

    /**
     * setDefaultAlias
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
     * cleanDefaultAlias
     *
     * @return mixed
     */
    public function cleanDefaultAlias()
    {
        $this->cleanAlias('');
    }

    /**
     * setAlias
     *
     * @param string $k key
     * @param mixed  $v value
     *
     * @return mixed
     */
    public function setAlias($k, $v=null)
    {
        set($this->_alias, $k, $v);
    }

    /**
     * cleanAlias
     *
     * @param array $arr array
     *
     * @return mixed
     */
    public function cleanAlias($arr=null)
    {
        clean($this->_alias, $arr);
    }
}
