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
 * PMVC Action
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
class ActionController
{
    /**
     * Mapping
     * @var ActionMappings
     */
    private $_mappings;
    /**
     * Request
     * @var HttpRequestServlet
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
        $this->_request = new Request();
        $this->_mappings = new ActionMappings();
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
        if (is_array($k)) {
            if (isset($k[_PLUGIN])) {
                initPlugIn($k[_PLUGIN]);
            }
        } elseif (_PLUGIN===$k) {
            initPlugIn($v);
        }
        $this->store($k, $v);
        call_plugin(
            'dispatcher',
            'set',
            array(
                'setOption',
                $k
            )
        );
    }

    /**
     * Store Option
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
     * Plug App
     *
     * @param string $parent   defaultAppFolder
     * @param array  $appAlias appAlias
     *
     * @return mixed
     */
    public function plugApp($parent=null, $appAlias=null)
    {
        call_plugin(
            'dispatcher',
            'notify',
            array(
                'MapRequest'
                ,true
            )
        );
        if (is_null($parent)) {
            $parent = $this->getAppParent();
        }
        if (!realpath($parent)) {
            return trigger_error('No App Parent found for '.$parent, E_USER_WARNING);
        } else {
            $app = $this->getApp();
            if (!empty($alias[$app])) {
                $app = $alias[$app];
            }
            $parent = lastSlash($parent);
            $path = $parent.$app.'/index.php';
        }
        if (!realpath($path)) {
            $app = getOption(_DEFAULT_APP);
            $path = $parent.$app.'/index.php';
        }
        if (!realpath($path)) {
            return trigger_error('No App found for '.$path, E_USER_WARNING);
        } else {
            addPlugInFolder($parent.$app.'/plugins');
            $this->setApp($app);
            $this->store(_RUN_PARENT, realpath($parent));
            $appPlugin = plug(
                _RUN_APP,
                array(
                    _PLUGIN_FILE=>$path
                )
            );
            $builder = $appPlugin[_INIT_BUILDER];
            if (empty($builder)) {
                return trigger_error('No builder found', E_USER_WARNING);
            }
            $this->setMapping($builder->getMappings());
            $action = $this->getAppAction();
            $action_path  = realpath($parent.$app.'/'.$action.'.php');
            if ($action_path) {
                l($action_path);
            }
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
        return $this->_mappings->set($mappings);
    }

    /**
     * Add mapping
     *
     * @param mixed $mappings mappings
     *
     * @return bool
     */
    public function addMapping($mappings)
    {
        return $this->_mappings->add($mappings);
    }

    /**
     * Process the request.
     *
     * @param mixed $mappings mappings
     *
     * @return mixed
     */
    public function process($mappings=null)
    {
        if (!is_null($mappings)) {
            if (!$this->addMapping($mappings)) {
                return false;
            }
        }
        call_plugin(
            'dispatcher',
            'notify',
            array(
                'MapRequest'
                ,true
            )
        );
        if (call_plugin('dispatcher', 'stop')) {
            return;
        }
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
     * @return ActionMapping
     */
    private function _processMapping()
    {
        $index = $this->getAppAction();
        if (!$this->_mappings->mappingExists($index)) {
            if ($this->_mappings->mappingExists('index')) {
                $index = 'index';
            }
        }
        $this->setAppAction($index);
        return $index;
    }
        
    /**
     * Execute mapping
     *
     * @param string $index pass run action
     *
     * @return ActionMapping
     */
    public function execute($index)
    {
        if (!$this->_mappings->mappingExists($index)) {
            return trigger_error(
                'No mappings found for index: '.$index,
                E_USER_WARNING
            );
        }
        $actionMapping = $this->_mappings->findMapping($index);
        $actionForm = $this->_processForm($actionMapping);
        $this->setOption(_RUN_FORM, $actionForm);
        if (!$actionForm) {
            $Errors = getOption(ERRORS);
            $actionForward = $this->getErrorForward(
                $actionMapping,
                $Errors[USER_ERRORS],
                $Errors[USER_LAST_ERROR]
            );
        } else {
            $actionForward = $this->_processAction(
                $actionMapping,
                $actionForm
            );
        }
        if (is_object($actionForward)) {
            return $this->_processForward($actionForward);
        } else {
            return $actionForward;
        }
    }

    /**
     * Init Error Action Forward 
     *
     * @param ActionMapping $actionMapping actionMapping
     * @param array         $errors        all errors
     * @param string        $last          last error 
     *
     * @return ActionForward
     */
    public function getErrorForward($actionMapping, $errors=null, $last=null)
    {
        $actionForward = $actionMapping['error'];
        $actionForward->set(
            array(
                'errors'=>$errors,
                'last'=>$last
            )
        );
        return $actionForward;
    }

    /**
     * ActionForm.
     *
     * @param ActionMapping $actionMapping actionMapping
     *
     * @return ActionForm
     */
    private function _processForm($actionMapping)
    {
        $actionForm = null;
        $form =& $this->_mappings->findForm(
            $actionMapping->form
        );
        if (!class_exists($form[_CLASS])) {
            $run_form = getOption(_RUN_FORM);
            if (!empty($run_form)) {
                return $run_form; 
            }
            $form[_CLASS] = getOption(
                _DEFAULT_FORM,
                __NAMESPACE__.'\ActionForm'
            );
        }
        $actionForm = new $form[_CLASS]($form);

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
     * Init Action Form Value
     *
     * @param ActionForm    $actionForm    actionForm
     * @param ActionMapping $actionMapping actionMapping
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
            $actionForm[$name] = $this->_request[$name];
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
        return (string)$actionForm->validate();
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
            'dispatcher',
            'notify',
            array(
                'B4ProcessAction',
                true
            )
        );
        $func = $actionMapping->func;
        if (!is_callable($func)) {
            return trigger_error(
                'parse action error, function not exists. '
                .print_r($func, true), E_USER_WARNING
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
            'dispatcher',
            'notify',
            array(
                'Finish'
                ,true
            )
        );
    }

    /**
     * Get Request
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get Mapping
     *
     * @return mixed
     */
    public function getMapping()
    {
        return $this->_mappings;
    }

    /**
     * GetApp
     *
     * @return mixed
     */
    public function getApp()
    {
        return option('get', _RUN_APP);
    }

    /**
     * SetApp
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
     * Get App Parent
     *
     * @return string
     */
    public function getAppParent()
    {
        return option('get', _RUN_PARENT);
    }

    /**
     * Get App Action
     *
     * @return mixed
     */
    public function getAppAction()
    {
        return option('get', _RUN_ACTION);
    }

    /**
     * Set App Action
     *
     * @param string $action action
     *
     * @return mixed
     */
    public function setAppAction($action)
    {
        return $this->setOption(_RUN_ACTION, $action);
    }
}
