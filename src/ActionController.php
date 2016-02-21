<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category CategoryName
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

/**
 * PMVC Action.
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
class ActionController
{
    /**
     * Mapping.
     *
     * @var ActionMappings
     */
    private $_mappings;
    /**
     * Request.
     *
     * @var HttpRequestServlet
     */
    private $_request;

    /**
     * ActionController construct with the options.
     *
     * @param array $options options
     */
    public function __construct($options = null)
    {
        $this->store(CONTROLLER, $this);
        if ($options) {
            $this->setOption($options);
        }
        $this->_request = new Request();
        $this->_mappings = new ActionMappings();
    }

    /**
     * Set option (Will trigger Event).
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return void
     */
    public function setOption($k, $v = null)
    {
        $this->store($k, $v);
        if (isContain($k, _PLUGIN)) {
            initPlugIn(getOption(_PLUGIN));
        }
        call_plugin(
            'dispatcher',
            'set',
            [
                'setOption',
                $k,
            ]
        );
    }

    /**
     * Store Option (Will not trigger Event).
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return void
     */
    public function store($k, $v = null)
    {
        option('set', $k, $v);
    }

    /**
     * Plug App.
     *
     * @param string $parent   defaultAppFolder
     * @param array  $appAlias appAlias
     *
     * @return mixed
     */
    public function plugApp($parent = null, $appAlias = null)
    {
        call_plugin(
            'dispatcher',
            'notify',
            [
                Event\MAP_REQUEST, true,
            ]
        );
        if (is_null($parent)) {
            $parent = $this->getAppParent();
        }
        if (!realpath($parent)) {
            return !trigger_error(
                'No App Parent found for '.$parent,
                E_USER_WARNING
            );
        } else {
            $app = $this->getApp();
            if (!empty($appAlias[$app])) {
                $app = $appAlias[$app];
            }
            $parent = lastSlash($parent);
            $path = $parent.$app.'/index.php';
        }
        if (!realpath($path)) {
            $app = getOption(_DEFAULT_APP);
            $path = $parent.$app.'/index.php';
        }
        if (!realpath($path)) {
            return !trigger_error('No App found for '.$path, E_USER_WARNING);
        } else {
            addPlugInFolder($parent.$app.'/plugins');
            $this->setApp($app);
            $this->setOption(
                _RUN_PARENT,
                realpath($parent)
            );
            $appPlugin = plug(
                _RUN_APP,
                [
                    _PLUGIN_FILE => $path,
                ]
            );
            $builder = $appPlugin[_INIT_BUILDER];
            if (empty($builder)) {
                return !trigger_error('No builder found', E_USER_WARNING);
            }
            $action = $this->getAppAction();
            if ($appPlugin->isCallAble($action)) {
                $appPlugin->{$action}();
            }

            return $this->setMapping($builder());
        }
    }

    /**
     * Set mapping.
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
     * Add mapping.
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
     * @param MappingBuilder $builder Get mappings
     *
     * @return mixed
     */
    public function __invoke(MappingBuilder $builder = null)
    {
        if (!is_null($builder)) {
            $this->addMapping($builder());
        }
        call_plugin(
            'dispatcher',
            'notify',
            [
                Event\MAP_REQUEST, true,
            ]
        );
        if (call_plugin('dispatcher', 'stop')) {
            return;
        }
        $forward = (object) [
            'action' => $this->getAppAction(),
        ];
        $results = [];
        while (
            isset($forward->action) &&
            $forward = $this->execute($forward->action)
        ) {
            if (isset($forward->result)) {
                $results[] = $forward->result;
            } else {
                $results[] = $forward;
            }
        }
        $this->_finish();

        return $results;
    }

    /**
     * Execute mapping.
     *
     * @param string $index pass run action
     *
     * @return ActionMapping
     */
    public function execute($index)
    {
        $actionMapping = $this->_processMapping($index);
        $actionForm = $this->_processForm($actionMapping);
        $this->setOption(_RUN_FORM, $actionForm);
        if (!$actionForm) {
            $actionForward = $this->getErrorForward();
        } else {
            $actionForward = $this->_processAction(
                $actionMapping,
                $actionForm
            );
        }

        return $this->processForward($actionForward);
    }

    /**
     * ActionMapping.
     *
     * @param string $index mapping or action index 
     *
     * @return ActionMapping
     */
    private function _processMapping($index)
    {
        $m = $this->_mappings;
        if (!$m->mappingExists($index)) {
            if ($this->_mappings->mappingExists('index')) {
                $index = 'index';
            }
        }
        if (!$m->mappingExists($index)) {
            return !trigger_error(
                'No mappings found for index: '.$index,
                E_USER_WARNING
            );
        }

        return $m->findMapping($index);
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
        $form = &$this->_mappings->findForm(
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
                $actionForm = false;
            }
        }

        return $actionForm;
    }

    /**
     * Init Action Form Value.
     *
     * @param ActionForm    $actionForm    actionForm
     * @param ActionMapping $actionMapping actionMapping
     *
     * @return ActionForm
     */
    private function _initActionFormValue($actionForm, $actionMapping)
    {
        $scope = &$actionMapping->scope;
        if (!is_array($scope)) {
            $scope = $this->_request->keySet();
        }
        foreach ($scope as $name) {
            if (is_array($name)) {
                $actionForm[$name[1]] = $this->_request[$name[0]];
            } else {
                $actionForm[$name] = $this->_request[$name];
            }
        }
    }

    /**
     * Call the validate() by ActionForm.
     *
     * @param ActionForm $actionForm actionForm
     *
     * @return bool
     */
    private function _processValidate($actionForm)
    {
        return (string) $actionForm->validate();
    }

    /**
     * Action for this request.
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
            [
                Event\B4_PROCESS_ACTION,
                true,
            ]
        );
        $func = $actionMapping->func;
        if (!is_callable($func)) {
            return !trigger_error(
                'parse action error, function not exists. '.
                print_r($func, true),
                E_USER_WARNING
            );
        }

        return call_user_func_array(
            $func,
            [$actionMapping, $actionForm]
        );
    }

    /**
     * ActionForward.
     *
     * @param ActionForward $actionForward actionForward
     *
     * @return mixed
     */
    public function processForward($actionForward)
    {
        call_plugin(
            'dispatcher',
            'notify',
            [
                Event\B4_PROCESS_FORWARD, true,
            ]
        );
        if (is_callable([$actionForward, 'go'])) {
            return $actionForward->go();
        } else {
            return $actionForward;
        }
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
            [
                Event\FINISH, true,
            ]
        );
    }

    /**
     * Init Error Action Forward.
     *
     * @return ActionForward
     */
    public function getErrorForward()
    {
        call_plugin(
            'dispatcher',
            'notify',
            [
                Event\B4_PROCESS_ERROR, true,
            ]
        );
        if (!$this->_mappings->forwardExists('error')) {
            return false;
        }
        $errorForward = $this->_mappings->findForward('error');
        $AllErrors = getOption(ERRORS);
        $errorForward->set(
            [
                'errors'    => $AllErrors[USER_ERRORS],
                'lastError' => $AllErrors[USER_LAST_ERROR],
            ]
        );

        return $errorForward;
    }

    /**
     * Get Request.
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get Mapping.
     *
     * @return mixed
     */
    public function getMapping()
    {
        return $this->_mappings;
    }

    /**
     * GetApp.
     *
     * @return mixed
     */
    public function getApp()
    {
        return option('get', _RUN_APP);
    }

    /**
     * SetApp.
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
     * Get App Parent.
     *
     * @return string
     */
    public function getAppParent()
    {
        return option('get', _RUN_PARENT);
    }

    /**
     * Get App Action.
     *
     * @return mixed
     */
    public function getAppAction()
    {
        return option('get', _RUN_ACTION, '');
    }

    /**
     * Set App Action.
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
