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
class ActionController
{
    /**
     * @var	ActionMappings
     */
     private $_mappings;
    /**
     * @var	HttpRequestServlet
     */
     private $_request;

    /**
     * ActionController construct with the options.
     *
     * @param array $options options
     */
    public function __construct($options=null)
    {
        $this->store(CONTROLLER, $this);
        if ($options) {
            $this->setOption($options);
        }
        $this->_request =& new Request();
    }

    /**
     * Set option
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return void
     */
    public function setOption($k, $v=null)
    {
        $this->store($k, $v);
        if (is_array($k)) {
            if (isset($k[_PLUGIN])) {
                initPlugIn($k[_PLUGIN]);
            }
        }
        call_plugin(
            'observer',
            'set',
            array(
                'setOption',
                $k
            )
        );
    }

    /**
     * store
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return void
     */
    public function store($k, $v=null)
    {
        option('set', $k, $v);
    }

    /**
     * plugApp
     *
     * @param string $parent   defaultAppFolder
     * @param array  $appAlias appAlias
     * 
     * @return mixed 
     */
    public function plugApp($parent,$appAlias=null)
    {
        $app = $this->getApp();
        if (!empty($alias[$app])) {
            $app = $alias[$app];
        }
        if (!realpath($parent)) {
            trigger_error('No App Parent found for '.$parent);
            return false;
        } else {
            $path = lastSlash($parent).$app.'/index.php';
        }
        if (!realpath($path)) {
            trigger_error('No App found for '.$path);
            return false;
        } else {
            $appPlugin = plug(
                _RUN_APP,
                array(
                    _PLUGIN_FILE=>$path 
                )
            );
            $builder = $appPlugin->get(_INIT_BUILDER);
            if (empty($builder)) {
                trigger_error('No builder found');
                return false;
            }
            $this->setMapping($builder->getMappings()); 
            $this->store(_RUN_PARENT, realpath($parent));
            return true;
        }
    }


    /**
     * Set mapping
     * 
     * @param mixed $mappings mappings
     * 
     * @return bool
     */
    public function setMapping($mappings)
    {
        if (!empty($mappings)) {
            $this->_mappings = new ActionMappings($mappings);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process the request.
     * 
     * @return mixed 
     */
    public function process()
    {
        call_plugin(
            'observer',
            'fire',
            array(
                'MapRequest'
                ,true
            )
        );
        $index  = $this->_processMapping();
        $result = $this->execute($index);
        $this->_finish();
        if (!empty($result->lazyOutput)) {
            $this->execute($result->lazyOutput);
        }
        return $result;
    }

    /**
     * ActionMapping.
     *
     * @return	ActionMapping
     */
    private function _processMapping()
    {
        $index = option('get', _RUN_ACTION);
        if (!$this->_mappings->mappingExists($index)) {
            if ($this->_mappings->mappingExists('index')) {
                $index = 'index';
            }
        }
        $this->store(_RUN_ACTION, $index);
        return $index;
    }
        
    /**
     * execute mapping
     *
     * @param string $index pass run action
     * 
     * @return	ActionMapping
     */
    public function execute($index)
    {
        if (!$this->_mappings->mappingExists($index)) {
            trigger_error('No mappings found for index: '.$index);
        }
        $actionMapping = &$this->_mappings->findMapping($index);
        $actionForm = $this->_processForm($actionMapping);
        $actionForward = $this->_processAction(
            $actionMapping,
            $actionForm
        );
        if (is_object($actionForward)) {
            return $this->_processForward($actionForward);
        } else {
            return $actionForward;
        }
    }

    /**
     * ActionForm.
     *
     * @param array $actionMapping actionMapping
     * 
     * @return ActionForm
     */
    private function _processForm($actionMapping)
    {
        $actionForm = null;
        $name = $actionMapping->form;
        //verify that a form has been mapped
        if (strlen($name)) {
            $form =& $this->_mappings->findForm($name);
            $class = (!$form) ? $name : $form[_CLASS];
            if (!$class) {
                $class = 'ActionForm';
            }
            if (!class_exists($class)) {
                trigger_error(
                    'parse form error, not define class type', 
                    E_USER_ERROR
                );
            }
            //create and init form class
            $actionForm =& new $class();
        } else {
            $actionForm =& new ActionForm();
        }
        //add request parameters
        $this->_initActionFormValue($actionForm, $actionMapping);

        //validate the form if necesarry
        if ($actionMapping->validate) {
            if (!$this->_processValidate($actionForm)) {
                $actionForm=false;
            }
        }
        return $actionForm;
    }

    /**
     * initActionFormValue
     *
     * @param object $actionForm    actionForm
     * @param object $actionMapping actionMapping
     * 
     * @return ActionForm
     */
    private function _initActionFormValue($actionForm, $actionMapping)
    {
        $scope =& $actionMapping->scope;
        if (!is_array($scope)) {
            $scope = $this->_request->keySet();
        }
        foreach ($scope as $name) {
            $get = $this->_request->get($name);
            $actionForm->set($name, $get);
        }
    }

    /**
     * Call the validate() by ActionForm.
     *
     * @param ActionForm $actionForm actionForm
     *
     * @return boolean
     */
    private function _processValidate($actionForm)
    {
        return $actionForm->validate();
    }

    /**
     * Action for this request
     *
     * @param ActionMapping $actionMapping actionMapping
     * @param ActionForm    $actionForm    actionForm
     *
     * @return ActionForward
     */
    private function _processAction($actionMapping, $actionForm)
    {
        call_plugin(
            'observer', 
            'fire',
            array(
                'B4ProcessAction',
                true
            )
        );
        $func = $actionMapping->func;
        if (!is_callable($func)) {
            trigger_error(
                'parse action error, function not exists',
                E_USER_ERROR
            );
        }
        return call_user_func_array(
            $func,
            array($actionMapping, $actionForm, $this->_request)
        );
    }

    /**
     * ActionForward
     *
     * @param ActionForward $actionForward actionForward
     * 
     * @return mixed 
     */
    private function _processForward($actionForward)
    {
        if ($actionForward->clean) {
            $actionForward->clean();
        }
        return $actionForward->go();
    }

    /**
     * Finish off the request and take down the controller.
     * 
     * @return void
     */
    private function _finish()
    {
        call_plugin(
            'observer', 
            'fire', 
            array(
                'Finish'
                ,true
            )
        );
    }

    /**
     * getRequest
     * 
     * @return mixed 
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * getMapping
     * 
     * @return mixed 
     */
    public function getMapping()
    {
        return $this->_mappings;
    }

    /**
     * getApp 
     * 
     * @return mixed 
     */
    public function getApp()
    {
        return option('get', _RUN_APP);
    }

    /**
     * setApp 
     * 
     * @param string $app app
     *
     * @return mixed 
     */
    public function setApp($app)
    {
        return $this->setOption(_RUN_APP, $app);
    }

    /**
     * getApp 
     * 
     * @return string 
     */
    public function getAppParent()
    {
        return option('get', _RUN_PARENT); 
    }

    /**
     * getApp 
     * 
     * @return mixed 
     */
    public function getAppAction()
    {
        return option('get', _RUN_ACTION); 
    }

    /**
     * setAppAction
     * 
     * @param string $action action 
     *
     * @return mixed 
     */
    public function setAppAction($action)
    {
        return $this->setOption(_RUN_ACTION, $app);
    }
}
