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
     */
    public function __construct()
    {
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
        callPlugin(
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
     * @param string $parent    defaultAppFolder
     * @param array  $appAlias  appAlias
     * @param string $indexFile index.php
     *
     * @return mixed
     */
    public function plugApp($parent = null, $appAlias = null, $indexFile = 'index')
    {
        callPlugin(
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
                'No App Parent found for ['.$parent.']',
                E_USER_WARNING
            );
        } else {
            $app = $this->getApp();
            if (!empty($appAlias[$app])) {
                $app = $appAlias[$app];
            }
            $parent = lastSlash($parent);
            $path = $parent.$app.'/'.$indexFile.'.php';
        }
        if (!realpath($path)) {
            $app = getOption(_DEFAULT_APP);
            $path = $parent.$app.'/'.$indexFile.'.php';
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
        if (callPlugin('dispatcher', 'stop')) {
            // Stop for authentication plugin verify failed
            return;
        }
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\MAP_REQUEST, true,
            ]
        );
        if (!is_null($builder)) {
            $this->addMapping($builder());
        }
        $forward = (object) [
            'action' => $this->getAppAction(),
        ];
        $results = [];
        while (
            isset($forward->action) &&
            $forward = $this->execute($forward->action)
        ) {
            $results[] = $this->processForward($forward);
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
        //validate the form if necesarry
        if ($actionMapping->validate) {
            $errorForward = $this->_processValidate($actionForm);
        }
        if (!empty($errorForward)) {
            $actionForward = $errorForward;
        } else {
            $actionForward = $this->_processAction(
                $actionMapping,
                $actionForm
            );
        }

        return $actionForward;
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
        if (is_callable($form[_CLASS])) {
            $actionForm = call_user_func($form[_CLASS]);
        } else {
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
            $actionForm = new $form[_CLASS]();
        }

        //add request parameters
        $this->_initActionFormValue($actionForm, $actionMapping);

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
        $this->setOption(_SCOPE, $actionMapping);
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
     * @return bool if good to go return false else return true to block.
     */
    private function _processValidate($actionForm)
    {
        $isValid = (string) $actionForm->validate();
        $error = $this->getErrorForward();
        if ($error) {
            return $error;
        }

        return !$isValid;
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
        callPlugin(
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
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\B4_PROCESS_FORWARD,
                true,
            ]
        );
        if (callPlugin('dispatcher', 'stop')) {
            unset($actionForward->action);

            return;
        }
        if (is_callable([$actionForward, 'go'])) {
            return $actionForward->go();
        } else {
            return $actionForward;
        }
    }

    /**
     * Finish request and take down the controller.
     *
     * @return void
     */
    private function _finish()
    {
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\FINISH,
                true,
            ]
        );
        $errorForward = $this->getErrorForward();
        if ($errorForward) {
            $this->processForward($errorForward);
        }
    }

    /**
     * Init Error Action Forward.
     *
     * @return ActionForward
     */
    public function getErrorForward()
    {
        $AllErrors = getOption(ERRORS);
        if (empty($AllErrors[USER_LAST_ERROR])) {
            return false;
        }
        callPlugin(
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
