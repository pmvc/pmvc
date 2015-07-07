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
class ActionMapping extends HashMap
{
        
    /**
     * Func
     * @var string
     */
    public $func;
        
    /**
     * Form
     * @var string
     */
    public $form;

    /**
     * Validate
     * @see ActionController::_processForm
     * @var bool
     */
    public $validate = true;
        
    /**
     * Set request scope , post or get
     * @see ActionController::initActionFormValue
     * @var string
     */
    public $scope;
        
    /**
     * Set the initial field for uri type request
     * @see ActionController::initActionFormValue
     * @var string 
     */
    public $initial;
        
    /**
     * This action mapping name
     * @var string 
     */
    public $name;
        
    /**
     * To set true if don't leave request to next request
     * @see FormBack
     * @var bool 
     */
    public $clean;

    /**
     * Type
     * @see FormBack
     * @var string [read|write]
     * save : to set with a insert or update db action
     * show : to set wiht a create_form or edit_form action
     */
    public $type;

    /**
     * ActionMapping
     *
     * @param array  $mapping mapping
     * @param string $name    name
     */
    public function __construct(&$mapping, $name)
    {
        $this->name= $name;
        if (isset($mapping[_FUNCTION])) {
            $this->func = $mapping[_FUNCTION];
        } else {
            $this->func = $this->name;
        }
        if (isset($mapping[_FORM])) {
            $this->form = $mapping[_FORM];
        }
        if (isset($mapping[_VALIDATE])) {
            $this->validate = $mapping[_VALIDATE];
        }
        if (isset($mapping[_CLEAN])) {
            $this->clean=$mapping[_CLEAN];
        }
        if (isset($mapping[_TYPE])) {
            $this->type=$mapping[_TYPE];
        }
        if (isset($mapping[_SCOPE])) {
            $this->scope=$mapping[_SCOPE];
        }
        if (isset($mapping[_INITIAL])) {
            $this->initial=$mapping[_INITIAL];
        }
    }

    /**
     * Get ActionForwards from ActionMapping
     *
     * @param array $name name
     *
     * @return mixed
     */
    public function offsetGet($name)
    {
        $forward = getC()->getMapping()->findForward($name);
        if ($forward) {
            return new ActionForward($forward);
        } else {
            trigger_error('Forward key: {'.$name.'} not exists');
            return false;
        }
    }
}
