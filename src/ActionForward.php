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
	 * @var	string
	 */
	private $_path;

	/**
	 * @var	string
	 */
	private $_type;

        /**
        * header
        * @var array
        */
        private $_header=array();

        /**
         * @var string slower action 
         */
        public $slower;

        /**
         * @var object default view engine
         */
        public $view;

        /**
	 * @var	string
	 */
	public $clean=false;

	/**
	 * ActionForward
	 *
	 * @access	public
	 * @param	string	$name
	 * @param	array	$forward
	 */
	function __construct($forward)
	{
            $this->setPath($forward[_PATH]);
            $this->setType($forward[_TYPE]);
            $this->setHeader($forward[_HEADER]);
            $this->clean = $forward[_CLEAN];
            $this->slower = $forward[_SLOWER];
            if(is_array($forward[_INITIAL])){
                 $this->clean($forward[_INITIAL]);
            }
        }

        /**
        * get header
        */
        function getHeader()
        {
            return $this->_header;
        }

        /**
        * set header
        */
        function setHeader($v)
        {
            set($this->_header,$v);
        }

        /**
         * set type
         */
        function setType($type=null)
        {
            if(is_null($type)){
                $type='redirect';
            } elseif ('view'==$type){
                $this->view=plug('view');
            }
            $this->_type = $type;
        }

        /**
        * get type
        */
        function getType()
        {
            return $this->_type;
        }

	/**
	 * Get the path of the ActionForward.
	 *
	 * @access	public
	 * @return	string
	 */
	function getPath($bMerge=null)
	{
            $path = $this->_path;
            if($bMerge){
                $attribute=$this->get();
                unset($attribute[_CLASS]);
                $path = plug(getOption(_ROUTING))->joinQuery($path,$attribute);
            }
            return $path;
	}

	/**
	 * Set the path of the ActionForward.
	 *
	 * @access	public
	 * @param	string	$path
	 */
	function setPath($path)
	{
            $this->_path = $path;
	}

	/**
         * Set ActionFored key and value
	 * @access	public
	 * @param	string	$key
	 * @param	string	$value
	 */
        function set($k,$v=null)
        {
            if('view'==$this->getType()){
                $args = func_get_args();
                return call_user_func_array(array($this->view,'set'),$args);
            }else{
                return parent::set($k,$v);
            }
        }

        function &get($k)
        {
            if('view'==$this->getType()){
                $args = func_get_args();
                $return = call_user_func_array(array($this->view,'get'),$args);
                return $return;
            }else{
                return parent::get($k);
            }
        }

        function _processView()
        {
            $this->view->folder = getOption(_TEMPLATE_DIR);
            $this->view->path = $this->getPath();
            $this->view->process();
            if(!empty($this->slower)){
                return $this;
            }
        }

        /**
         * execute ActionForward
         */
        function go()
        {
            switch($this->getType()){
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
?>
