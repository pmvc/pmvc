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
 * PMVC ActionForward
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     http://pear.php.net/package/PackageName
 */
class ActionForward extends HashMap
{
    /**
     * Path
     * @var string
     */
    private $_path;

    /**
     * Type
     * @var string
     */
    private $_type;

    /**
     * Header
     * @var array
     */
    private $_header=array();

    /**
     * LazyOutput action
     * @var string 
     */
    public $lazyOutput;

    /**
     * Default view engine
     * @var object
     */
    public $view;

    /**
     * Clean
     * @var string
     */
    public $clean=false;

    /**
     * ActionForward
     *
     * @param array $forward forward
     */
    public function __construct($forward)
    {
        $this->setPath($forward[_PATH]);
        $this->setType($forward[_TYPE]);
        $this->setHeader($forward[_HEADER]);
        $this->clean = $forward[_CLEAN];
        $this->lazyOutput = $forward[_LAZY_OUTPUT];
        if (is_array($forward[_INITIAL])) {
            $this->clean($forward[_INITIAL]);
        }
    }

    /**
     * Get header
     *
     * @return array header
     */
    public function getHeader()
    {
        return $this->_header;
    }

    /**
     * Set header
     * 
     * @param array $v value
     *
     * @return mixed
     */
    public function setHeader($v)
    {
        return set($this->_header, $v);
    }

    /**
    * Set type
    *
    * @param string $type type
    *
    * @return void
    */
    public function setType($type=null)
    {
        if (is_null($type)) {
            $type='redirect';
        } elseif ('view'==$type) {
            $this->view=plug('view');
            $this->view['forward'] = $this;
        }
        $this->_type = $type;
    }

    /**
    * Get type
    * 
    * @return string
    */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get the path of the ActionForward.
     *
     * @param bool $bMerge merge or not 
     * 
     * @return string
     */
    public function getPath($bMerge=false)
    {
        $path = $this->_path;
        if ($bMerge) {
            $attribute=$this->get();
            unset($attribute[_CLASS]);
            $path = plug(getOption(_ROUTING))->joinQuery($path, $attribute);
        }
        return $path;
    }

    /**
     * Set the path of the ActionForward.
     *
     * @param string $path path
     *
     * @return void
     */
    public function setPath($path)
    {
        $this->_path = $path;
    }

    /**
     * Set ActionFored key and value
     *
     * @param string $k key
     * @param string $v value
     * 
     * @return bool
     */
    public function set($k, $v=null)
    {
        if ('view'==$this->getType()) {
            $args = func_get_args();
            return call_user_func_array(array($this->view,'set'), $args);
        } else {
            return $this[$k]=$v;
        }
    }

    /**
     * Get
     * 
     * @param mixed $k       key
     * @param mixed $default default 
     *
     * @return mixed
     */
    public function get( $k=null, $default=null )
    {
        if ('view'==$this->getType()) {
            $args = func_get_args();
            $return = call_user_func_array(array($this->view,'get'), $args);
            return $return;
        } else {
            return get($this, $k, $default);
        }
    }
    /**
     * Process View
     *
     * @return $this
     */
    private function _processView()
    {
        $this->view->setThemeFolder(
            getOption(_TEMPLATE_DIR)
        );
        $this->view->setThemePath($this->getPath());
        $this->view->process();
        if (!empty($this->lazyOutput)) {
            return $this;
        }
    }

    /**
     * Execute ActionForward
     *
     * @return mixed
     */
    public function go()
    {
        switch ($this->getType()) {
        case 'view':
            return $this->_processView();
            break;
        case 'action':
        case 'redirect':
        default:
            $path = $this->getPath();
            break;
        }
    }
}
