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
 * PMVC ActionMapping
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com> 
 * @license  http://opensource.org/licenses/MIT MIT 
 * @link     http://pear.php.net/package/PackageName
 */
class ActionMapping extends Object
{
	/**
	 * @var	string
	 */
        var $_class;
        
        /**
	 * @var	string
	 */
	var $_func;
        
        /**
	 * @var	string
	 */
	var $_type;
        
        /**
	 * @var	string
	 */
	var $_form;
        
        
        /**
         * @see ActionController::_processForm
	 * @var	bool
	 */
	var $validate = true;
        
        /**
         * @see ActionController::initActionFormValue
         * @var	string set request scope , post or get
	 */
	var $scope;
        
        /**
         * @see ActionController::initActionFormValue
         * @var	string set the initial field for uri type request
	 */
	var $initial;
        
        /**
         * @var string this action mapping name
         */
        var $name;
        
        /**
         * @see FormBack
	 * @var	bool to set true if don't leave request to next request
	 */
        var $clean;

        /**
         * @see FormBack
         * @var	string [read|write]
         * save : to set with a insert or update db action
         * show : to set wiht a create_form or edit_form action
	 */
        var $type;

        /**
         * @var bool|string 
         * true : always enable cache
         * false: disable cache
         * int : enable cache and set expire time
         */
        var $cache;

        /**
	 * ActionMapping
	 *
	 * @access	public
	 * @param	array	$mapping
	 */
	function __construct(&$mapping,$name)
	{
            $this->name= $name;
            if(isset($mapping[_CLASS])){
                $this->_class = $mapping[_CLASS];
            }else{
                $this->_class = getC()->_mappings->getDefaultClass();
            }
            if(isset($mapping[_FUNCTION])){
                $this->_func = $mapping[_FUNCTION];
            }else{
                $this->_func = $this->name;
            }
            if(isset($mapping[_FORM])){
                $this->_form = $mapping[_FORM];
            }
            if(isset($mapping[_VALIDATE])){
                $this->validate = $mapping[_VALIDATE];
            }
            if(isset($mapping[_CLEAN])){
               $this->clean=$mapping[_CLEAN];
            }
            if(isset($mapping[_TYPE])){
                 $this->type=$mapping[_TYPE];
            }
            if (isset($mapping[_SCOPE])){
                 $this->scope=$mapping[_SCOPE];
            }
            if (isset($mapping[_INITIAL])){
                $this->initial=$mapping[_INITIAL];
            }
            if (isset($mapping[_CACHE])){
                $this->cache=$mapping[_CACHE];
            }
        }

	/**
         * Get ActionForwards from ActionMapping
	 *
	 * @access	private
	 * @param	array	$forwards
	 */
        function get($name){
            $forward = getC()->getMapping()->findForward($name);
            if($forward){
            	return new ActionForward($forward);
            }else{
                trigger_error('Forward key: {'.$name.'} not exists');
                return false;
            }
        }

	/**
	 * Get the object class
	 *
	 * @access	public
	 * @return	string
	 */
	function getClass()
	{
	    return $this->_class;
        }


        /**
         * get controller function
         */
        function getFunc(){
            return $this->_func;
        }

	/**
	 * Get the form 
	 *
	 * @access	public
	 * @return	string
	 */
	function getForm()
        {
            return $this->_form;
        }


}
?>
