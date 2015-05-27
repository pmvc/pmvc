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
 * PMVC Action
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     http://pear.php.net/package/PackageName
 */
class PlugIn extends HashMap
{
    /**
     * @var string
     */
    public $name;
    public $file;
    private $_alias;

    /**
     * get dir
     *
     * @return mixed
     */
    public function getDir()
    {
        return dirname($this->file).'/';
    }

    /**
     * init
     *
     * @return mixed
     */
    public function init()
    {
    }

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
            $r=call_user_func_array(
                array($this->_alias[''],$method),
                $args
            );
        }
        return $r;
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

    /**
     * observer update function
     *
     * @param object $observer observer
     * @param mixed  $state    state
     *
     * @return mixed
     */
    public function update($observer=null, $state=null)
    {
        if (!is_null($state) && method_exists($this, 'on'.$state)) {
            $r=call_user_func(
                array(&$this, 'on'.$state), $observer, $state
            );
            return $r;
        }
        return $this;
    }
}
